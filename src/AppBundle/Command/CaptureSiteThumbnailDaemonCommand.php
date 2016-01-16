<?php

namespace Undine\AppBundle\Command;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\RejectedPromise;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Undine\Loop\CallbackOptions;
use Undine\Repository\SiteRepository;

class CaptureSiteThumbnailDaemonCommand extends ContainerAwareCommand
{
    /**
     * @var string
     */
    private $phantomJsPath;

    /**
     * @var callable
     */
    private $loopHandler;

    /**
     * @var SiteRepository
     */
    private $siteRepository;

    /**
     * @var string
     */
    private $thumbnailDir;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * Maximum thumbnail age; when it's considered stale.
     * This is a string compatible with PHP's strtotime() function.
     *
     * @see strtotime
     *
     * @var string
     */
    private $maxAge;

    /**
     * @param string          $phantomJsPath
     * @param callable        $loopHandler
     * @param SiteRepository  $siteRepository
     * @param string          $thumbnailDir
     * @param LoggerInterface $logger
     */
    public function __construct($phantomJsPath, callable $loopHandler, SiteRepository $siteRepository, $thumbnailDir, LoggerInterface $logger)
    {
        $this->phantomJsPath = realpath($phantomJsPath);
        $this->siteRepository = $siteRepository;
        $this->loopHandler = $loopHandler;
        $this->thumbnailDir = $thumbnailDir;
        $this->logger = $logger;
        $this->fs = new Filesystem();
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('app:capture-site-thumbnail-daemon')
            ->setDescription('Run a daemon that polls the database for websites without or with stale thumbnails.')
            ->addOption('children', 'c', InputOption::VALUE_REQUIRED, 'How many asynchronous processes to keep.', 1)
            ->addOption('max-age', 'a', InputOption::VALUE_REQUIRED, '', '-1 day');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $children = (string)$input->getOption('children');
        $this->maxAge = (string)$input->getOption('max-age');

        if ((int)$children < 0 || !ctype_digit($children)) {
            throw new \InvalidArgumentException('Number of children must be greater than 0.');
        }

        if (strtotime($this->maxAge) === false) {
            throw new \InvalidArgumentException(sprintf('The age "%s" is not valid.', $this->maxAge));
        }

        if (strtotime($this->maxAge) >= time()) {
            throw new \InvalidArgumentException(sprintf('The age "%s" is not set in the past.', $this->maxAge));
        }

        $children = (int)$children;
        $promises = [];

        for ($i = 0; $i < $children; ++$i) {
            // Delay a few ms, to reduce chance of race condition.
            $promises[] = $this->loop([$this, 'fetchThumbnail'], [CallbackOptions::DELAY => random_int(10, 200)]);
        }

        $this->logger->info('Initiated asynchronous thumbnail capture daemons.', [
            'childCount' => $children,
        ]);

        \GuzzleHttp\Promise\settle($promises)->wait();
    }

    /**
     * @internal
     */
    public function fetchThumbnail($retryCount = 0)
    {
        return (new FulfilledPromise(null))
            ->then(function () {
                $lockTimeout = (new \DateTime($this->maxAge));
                $siteInfo = $this->siteRepository->yieldSiteForThumbnailUpdate($lockTimeout);
                if ($siteInfo === null) {
                    $this->logger->info('No sites to update thumbnails, delay.');

                    return $this->loop([$this, 'fetchThumbnail'], [CallbackOptions::DELAY => 60000]);
                }
                $lockAcquired = $this->siteRepository->lockSiteForThumbnailUpdate($siteInfo->getId(), $lockTimeout);
                if (!$lockAcquired) {
                    $this->logger->info('Race condition occurred, retrying after a small delay.');

                    return $this->loop([$this, 'fetchThumbnail'], [CallbackOptions::DELAY => random_int(10, 200)]);
                }

                $capturePath = tempnam(sys_get_temp_dir(), 'pjs');
                $resizePath = tempnam(sys_get_temp_dir(), 'pjr');

                // See bin/phantomjs-capture.js for options.
                // There is a strange breaking point where a quality 89 generates a 52KB image,
                // but a quality of 90 generates a 5.5MB image.
                $quality = 89;
                $format = 'png';
                $width = 1024;
                $height = 582;

                $process = ProcessBuilder::create([$this->phantomJsPath, $siteInfo->getUrl(), $capturePath])
                    ->setPrefix('phantomjs')
                    ->add('--quality='.$quality)
                    ->add('--format='.$format)
                    ->add('--width='.$width)
                    ->add('--height='.$height)
                    ->getProcess();

                return $this->loop($process)
                    ->then(function (Process $process) use ($siteInfo, $capturePath, $resizePath) {
                        $currentHash = $siteInfo->getThumbnailPath() ? md5_file($this->thumbnailDir.$siteInfo->getThumbnailPath()) : null;

                        (new Imagine())
                            ->open($capturePath)
                            ->thumbnail(new Box(400, 256))
                            ->save($resizePath, ['format' => 'jpeg']);

                        // Remove the temp image and its traces.
                        $this->fs->remove($capturePath);

                        if (($newHash = md5_file($resizePath)) === $currentHash) {
                            $this->fs->remove($resizePath);
                            $this->logger->info('Site thumbnail did not change, update the timestamp only.', [
                                'siteId' => $siteInfo->getId(),
                                'thumbnailPath' => $siteInfo->getThumbnailPath(),
                            ]);
                            $this->siteRepository->updateSiteThumbnail($siteInfo->getId(), $siteInfo->getThumbnailPath());

                            return $this->loop([$this, 'fetchThumbnail']);
                        }

                        $thumbnailPath = sprintf('/%s_%s.jpg', $siteInfo->getId(), $newHash);
                        if ($siteInfo->getThumbnailPath() !== null) {
                            // Remove the old thumbnail.
                            $this->fs->remove($this->thumbnailDir.$siteInfo->getThumbnailPath());
                        }
                        $this->fs->rename($resizePath, $this->thumbnailDir.$thumbnailPath, true);

                        $this->siteRepository->updateSiteThumbnail($siteInfo->getId(), $thumbnailPath);

                        $this->logger->info('Site thumbnail captured.', [
                            'siteId' => $siteInfo->getId(),
                            'thumbnailPath' => $thumbnailPath,
                        ]);

                        return $this->loop([$this, 'fetchThumbnail']);
                    })
                    ->otherwise(function ($reason) use ($capturePath, $resizePath) {
                        // An exception was thrown, clean-up files, then leave the rest to the error handler.
                        try {
                            $this->fs->remove([$capturePath, $resizePath]);
                        } catch (IOException $e) {
                        }

                        return new RejectedPromise($reason);
                    });
            })
            ->otherwise(function ($reason) use ($retryCount) {
                /** @var \Exception $exception */
                $exception = \GuzzleHttp\Promise\exception_for($reason);
                $this->logger->log($retryCount >= 60 ? LogLevel::CRITICAL : LogLevel::ERROR, 'An error occurred in the daemon.', [
                    'message' => $exception->getMessage(),
                ]);
                $retryCount++;

                return $this->loop([$this, 'fetchThumbnail'], [CallbackOptions::ARGS => [$retryCount], CallbackOptions::DELAY => min($retryCount, 60) * 1000]);
            });
    }

    /**
     * @param mixed $asyncOperation
     * @param array $options
     *
     * @return Promise
     */
    private function loop($asyncOperation, array $options = [])
    {
        return call_user_func($this->loopHandler, $asyncOperation, $options);
    }
}
