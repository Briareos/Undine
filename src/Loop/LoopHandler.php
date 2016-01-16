<?php

namespace Undine\Loop;

use GuzzleHttp\Handler\CurlFactory;
use GuzzleHttp\Handler\CurlFactoryInterface;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\TaskQueue;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class LoopHandler
{
    const TYPE_HTTP = 1;
    const TYPE_PROCESS = 2;
    const TYPE_CALLABLE = 3;

    /**
     * @var HandleFactory[]
     */
    private $factory;

    /**
     * @var int
     */
    private $selectTimeout = 1;

    /**
     * @var int
     */
    private $activeHttp = 0;

    /**
     * @var int
     */
    private $activeProcess = 0;

    /**
     * @var resource|null
     */
    private $multiHandle;

    /**
     * Internal request counter. Goes from -1 and down, so to not collide with curl resources that go up.
     * This counter is shared between callable and process requests.
     *
     * @var int
     */
    private $index = 0;

    /**
     * @var array[]
     */
    private $httpHandles = [];

    /**
     * @var bool
     */
    private $httpNeedsExec = false;

    /**
     * @var array[]
     */
    private $processHandles = [];

    /**
     * @var array
     */
    private $delays = [];

    /**
     * @var int
     */
    private $processTimeoutChecker = 0;

    public function __construct()
    {
        $this->factory[self::TYPE_HTTP] = new CurlFactory(50);
        $this->factory[self::TYPE_PROCESS] = new HandleFactory();
        $this->factory[self::TYPE_CALLABLE] = new HandleFactory();
    }

    public function __destruct()
    {
        if ($this->multiHandle !== null) {
            curl_multi_close($this->multiHandle);
        }
        foreach ($this->processHandles as $entry) {
            /** @var Promise $promise */
            $promise = $entry['deferred'];
            $promise->cancel();
        }
        $this->delays = [];
    }

    /**
     * @param Process|RequestInterface|callable $request
     * @param array                             $options
     *
     * @return Promise Resolves to ProcessResult, ResponseInterface or whatever the callable returns, respectively depending on the first argument.
     */
    public function __invoke($request, array $options)
    {
        if ($request instanceof RequestInterface) {
            $type = self::TYPE_HTTP;
        } elseif ($request instanceof Process) {
            $type = self::TYPE_PROCESS;
        } elseif (is_callable($request)) {
            $type = self::TYPE_CALLABLE;
        } else {
            throw new \InvalidArgumentException(sprintf('The parameter must be a an instance of "%s", "%s" or a callable.', Process::class, RequestInterface::class));
        }

        $easy = $this->factory[$type]->create($request, $options);

        if ($type === self::TYPE_HTTP) {
            $id = (int)$easy->handle;
        } else {
            $id = --$this->index;
        }

        $promise = new Promise(
            [$this, 'execute'],
            function () use ($id, $type) {
                switch ($type) {
                    case self::TYPE_HTTP:
                        return $this->cancelHttp($id);
                    case self::TYPE_PROCESS:
                        return $this->cancelProcess($id);
                    default:
                        // Only delayed callables can be cancelled.
                        return $this->cancelCallable($id);
                }
            }
        );

        $entry = [
            'id' => $id,
            'easy' => $easy,
            'deferred' => $promise,
            'type' => $type,
        ];

        if (empty($options[RequestOptions::DELAY])) {
            $this->addRequest($entry);
        } else {
            $this->delays[$id] = $entry + ['delay' => microtime(true) + ($options[RequestOptions::DELAY] / 1000)];
        }

        return $promise;
    }

    private function addRequest(array $entry)
    {
        /** @var int $id */
        $id = $entry['id'];
        /** @var LoopHandle $easy */
        $easy = $entry['easy'];

        switch ($entry['type']) {
            case self::TYPE_HTTP:
                $this->httpHandles[$id] = $entry;
                // This will get overridden in curl_multi_exec(), but fill it here so we don't treat the first tick
                // like it doesn't have any ongoing requests and hence wait for delayed requests.
                ++$this->activeHttp;
                \GuzzleHttp\Promise\queue()->add(function () use ($easy) {
                    if ($this->multiHandle === null) {
                        $this->multiHandle = curl_multi_init();
                    }
                    $this->httpNeedsExec = true;
                    curl_multi_add_handle($this->multiHandle, $easy->handle);
                });
                break;
            case self::TYPE_PROCESS:
                $this->processHandles[$id] = $entry;
                ++$this->activeProcess;
                \GuzzleHttp\Promise\queue()->add([$easy->handle, 'start']);
                break;
            case self::TYPE_CALLABLE:
                \GuzzleHttp\Promise\queue()->add(static function () use ($entry) {
                    /** @var Promise $deferred */
                    $deferred = $entry['deferred'];
                    /** @var callable $fn */
                    $fn = $entry['easy']->handle;
                    try {
                        if (isset($entry['easy']->options[CallbackOptions::ARGS])) {
                            $value = call_user_func_array($fn, $entry['easy']->options[CallbackOptions::ARGS]);
                        } else {
                            $value = $fn();
                        }
                    } catch (\Exception $e) {
                        $deferred->reject($e);

                        return;
                    }
                    $deferred->resolve($value);
                });
                break;
        }
    }

    private function cancelHttp($id)
    {
        // Cannot cancel if it has been processed.
        if (!isset($this->httpHandles[$id])) {
            return false;
        }

        $handle = $this->httpHandles[$id]['easy']->handle;
        if ($this->multiHandle !== null) {
            curl_multi_remove_handle($this->multiHandle, $handle);
        }
        curl_close($handle);

        unset($this->delays[$id], $this->httpHandles[$id]);

        return true;
    }

    private function cancelCallable($id)
    {
        // Cannot cancel if it has been processed.
        if (!isset($this->delays[$id])) {
            return false;
        }

        unset($this->delays[$id]);

        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    private function cancelProcess($id)
    {
        // Cannot cancel if it has been processed.
        if (!isset($this->processHandles[$id])) {
            return false;
        }

        /** @var Process $process */
        $process = $this->processHandles[$id]['easy']->handle;
        $options = $this->processHandles[$id]['easy']->options;
        $timeout = isset($options[ProcessOptions::STOP_TIMEOUT]) ? $options[ProcessOptions::STOP_TIMEOUT] : 10000;
        $signal = isset($options[ProcessOptions::STOP_SIGNAL]) ? $options[ProcessOptions::STOP_SIGNAL] : null;
        $process->stop($timeout / 1000, $signal);

        unset($this->delays[$id], $this->processHandles[$id]);

        return true;
    }

    public function execute()
    {
        /** @var TaskQueue $queue */
        $queue = \GuzzleHttp\Promise\queue();

        // There may be immediate callbacks, so we can execute them here.
        $queue->run();

        while ($this->httpHandles || $this->processHandles || !$queue->isEmpty() || $this->delays) {
            // If there are no transfers, then sleep for the next delay
            if (!$this->activeHttp && !$this->activeProcess && $this->delays) {
                usleep($this->timeToNext());
            }
            $this->tick();
        }
    }

    private function addDelays()
    {
        if (!$this->delays) {
            return;
        }
        $currentTime = microtime(true);
        foreach ($this->delays as $id => $entry) {
            if ($currentTime >= $entry['delay']) {
                unset($this->delays[$id], $entry['delay']);
                $this->addRequest($entry);
            }
        }
    }

    private function tick()
    {
        $this->addDelays();

        \GuzzleHttp\Promise\queue()->run();

        while ($this->activeProcess) {
            // Less performant loop that uses sleep() instead of select() to be compatible with cURL.
            $this->processProcessMessages();
            if ($this->activeHttp) {
                do {
                    while (curl_multi_exec($this->multiHandle, $this->activeHttp) === CURLM_CALL_MULTI_PERFORM) {
                    }
                } while ($this->processHttpMessages());
            }

            usleep(100000);
            $this->addDelays();
        }

        if (!$this->activeHttp) {
            return;
        }

        // If we get here that means our loop is pure HTTP.

        do {
            // Step through the task queue which may add additional requests.
            \GuzzleHttp\Promise\queue()->run();

            $queueClean = true;

            if ($this->httpNeedsExec) {
                $queueClean = false;
                do {
                    while (curl_multi_exec($this->multiHandle, $this->activeHttp) === CURLM_CALL_MULTI_PERFORM) {
                    }
                    $this->processHttpMessages();
                } while ($this->processHttpMessages());
            }
        } while (!$queueClean);

        if ($this->activeHttp && curl_multi_select($this->multiHandle, $this->selectTimeout) === -1) {
            // Perform a usleep if a select returns -1.
            // See: https://bugs.php.net/bug.php?id=61141
            usleep(250);
        }

        do {
            while (curl_multi_exec($this->multiHandle, $this->activeHttp) === CURLM_CALL_MULTI_PERFORM) {
            }
        } while ($this->processHttpMessages());
    }

    private function processHttpMessages()
    {
        $this->httpNeedsExec = false;
        while ($done = curl_multi_info_read($this->multiHandle)) {
            $id = (int)$done['handle'];
            curl_multi_remove_handle($this->multiHandle, $done['handle']);
            if (!isset($this->httpHandles[$id])) {
                // Probably was cancelled.
                continue;
            }
            $entry = $this->httpHandles[$id];
            unset($this->httpHandles[$id], $this->delays[$id]);
            $entry['easy']->errno = $done['result'];
            /** @var Promise $deferred */
            $deferred = $entry['deferred'];
            /** @var CurlFactoryInterface $factory */
            $factory = $this->factory[self::TYPE_HTTP];
            $deferred->resolve(CurlFactory::finish(
                $this,
                $entry['easy'],
                $factory
            ));
        }

        return $this->httpNeedsExec;
    }

    private function processProcessMessages()
    {
        reset($this->processHandles);
        while ($entry = current($this->processHandles)) {
            next($this->processHandles);
            /** @var LoopHandle $easy */
            $easy = $entry['easy'];
            if ($easy->handle->isRunning()) {
                continue;
            }
            /** @var Promise $deferred */
            $deferred = $entry['deferred'];
            unset($this->processHandles[$entry['id']]);
            if ($easy->handle->isSuccessful()) {
                $deferred->resolve($easy->handle);
            } else {
                $deferred->reject(new ProcessFailedException($easy->handle));
            }
            --$this->activeProcess;
        }
    }

    private function timeToNext()
    {
        $currentTime = microtime(true);
        $nextTime = PHP_INT_MAX;
        foreach ($this->delays as $entry) {
            if ($entry['delay'] < $nextTime) {
                $nextTime = $entry['delay'];
            }
        }

        return max(0, $nextTime - $currentTime) * 1000000;
    }
}
