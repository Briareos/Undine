<?php

namespace Undine\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Undine\Model\Site;

class DrupalCronDaemonCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('daemon:drupal-cron:run');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop        = $this->getContainer()->get('event_loop');
        $conn        = $this->getContainer()->get('doctrine.dbal.default_connection');

        $selectQuery = $conn->prepare('SELECT s.url, s.state_cronKey AS cronKey FROM Site s WHERE s.status = :status AND s.state_cronLastRunAt >= :cronTime');

        $generator = function () use($selectQuery) {
            $selectQuery->execute([
                'status'   => Site::STATUS_CONNECTED,
                'cronTime' => time() - 3600 * 3,
            ]);

            while($row = $selectQuery->fetch(\PDO::FETCH_OBJ)) {
                yield $row;
            }
        };

        $callback = function(){

        };

        /** @noinspection PhpParamsInspection */
        $loop->addPeriodicTimer(60, $callback);

        $loop->run();
    }

}
