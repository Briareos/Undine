<?php

namespace Undine\Api\Error\Network;

use Undine\Api\Error\AbstractError;

class TimedOut extends AbstractError
{
    /**
     * @var int
     */
    private $timeout;

    /**
     * @param int $timeout
     */
    public function __construct($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'network.timed_out';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'timeout' => $this->timeout,
        ];
    }
}
