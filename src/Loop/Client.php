<?php

namespace Undine\Loop;

use GuzzleHttp\Promise\Promise;
use Symfony\Component\Process\Process;

class Client
{
    private $handler;

    /**
     * @param callable $handler
     */
    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param $cmd
     *
     * @return Promise
     */
    public function execute($cmd)
    {
        $fn = $this->handler;

        return $fn(new Process($cmd), []);
    }

    /**
     * @param callable $callable
     *
     * @return Promise
     */
    public function enqueue(callable $callable)
    {
        $fn = $this->handler;

        return $fn($callable, []);
    }

    /**
     * @param int      $timeout In seconds.
     * @param callable $callable
     *
     * @return Promise
     */
    public function enqueueIn($timeout, callable $callable)
    {
        $fn = $this->handler;

        return $fn($callable, ['delay' => $timeout * 1000]);
    }
}
