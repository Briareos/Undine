<?php

namespace Undine\AppBundle\Command;

use Doctrine\DBAL\Driver\Connection;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Promise\Promise;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CaptureSiteThumbnailDaemonCommand extends ContainerAwareCommand
{
    /**
     * @var string
     */
    private $binDir;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param string        $binDir
     * @param LoopInterface $loop
     * @param Connection    $connection
     */
    public function __construct($binDir, LoopInterface $loop, Connection $connection)
    {
        $this->binDir     = realpath($binDir);
        $this->loop       = $loop;
        $this->connection = $connection;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('app:capture-site-thumbnail-daemon');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $children = 2;

        $selectQuery = <<<SQL
SELECT s.id, s.url, s.http_username, s.http_password
FROM Site s
WHERE
  (
    # Thumbnail is not set...
    s.thumbnailUrl IS NULL
    # ... or thumbnail is at least 1 day old.
    OR (s.thumbnailUpdatedAt < DATE_SUB(:now, INTERVAL 1 DAY))
  )
ORDER BY
  # Prioritize sites without the thumbnail
  s.thumbnailUrl IS NULL DESC
SQL;

        $lockQuery = <<<SQL
UPDATE Site
    SET thumbnailLockAt = :now
WHERE id = :id
  AND (thumbnailLockAt IS NULL OR thumbnailLockAt < :lockTimeout)
SQL;

        $updateQuery = <<<SQL
UPDATE Site
SET
  thumbnailUrl = :thumbnailUrl,
  thumbnailUpdatedAt = :thumbnailUpdatedAt
  WHERE id = :id
SQL;

        $commit = \React\Promise\resolve()
            ->then(function(){
                $process = new Process(sprintf('phantomjs %s %s', escapeshellarg($this->binDir.'/phantomjs-capture.js'),json_encode(['url'=>(string)$url])));
            });

        for ($i = 0; $i < $children; $i++) {
            $this->loop->nextTick($commit);
        }

        $this->loop->run();
    }
}
