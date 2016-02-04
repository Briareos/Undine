<?php

namespace Undine\Guzzle\Middleware;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\RejectedPromise;
use Psr\Http\Message\RequestInterface;

class ThrottleMiddleware
{
    /**
     * @var callable
     */
    private $nextHandler;

    /**
     * @var array
     */
    private $running = [];

    /**
     * @var array
     */
    private $queue = [];

    /**
     * @param callable $nextHandler
     */
    public function __construct(callable $nextHandler)
    {
        $this->nextHandler = $nextHandler;
    }

    public static function create()
    {
        return function (callable $fn) {
            return new self($fn);
        };
    }

    public function __invoke(RequestInterface $request, array $options)
    {
        $fn = $this->nextHandler;

        $throttleId = isset($options['throttle_id']) ? $options['throttle_id'] : null;
        $limit = isset($options['throttle_limit']) ? $options['throttle_limit'] : null;

        if (!$throttleId || !$limit) {
            // Request is not throttled; just ignore it.
            return $fn($request, $options);
        }

        if (!isset($this->running[$throttleId])) {
            $this->running[$throttleId] = 0;
            $this->queue[$throttleId] = [];
        }

        $promise = new Promise([\GuzzleHttp\Promise\queue(), 'run']);

        if ($this->running[$throttleId] + 1 <= $limit) {
            // Queue has enough space; run this request and watch for queue size.
            ++$this->running[$throttleId];

            return $fn($request, $options)
                ->then($this->getQueuePopper($throttleId, true), $this->getQueuePopper($throttleId, false));
        }

        // The queue is full; delay the request, and don't forget to also pop the queue.
        $this->queue[$throttleId][] = function () use ($request, $options, $fn, $throttleId, $promise) {
            $promise->resolve(
                $fn($request, $options)
                    ->then($this->getQueuePopper($throttleId, true), $this->getQueuePopper($throttleId, false))
            );
        };

        return $promise;
    }

    private function getQueuePopper($queueId, $fulfilled)
    {
        return function ($value) use ($queueId, $fulfilled) {
            --$this->running[$queueId];
            if ($next = array_shift($this->queue[$queueId])) {
                /* @var callable $next */
                $next();
            }

            if ($fulfilled) {
                return $value;
            }

            return new RejectedPromise($value);
        };
    }
}
