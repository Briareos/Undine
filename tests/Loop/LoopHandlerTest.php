<?php

namespace Undine\Tests\Loop;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Undine\Guzzle\Middleware\ThrottleMiddleware;
use Undine\Loop\Client;
use Undine\Loop\LoopHandler;
use Undine\Tests\Loop\Resources\Server;

class LoopHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function processList()
    {
        return [
            [
                [
                    // First element is duration (in seconds), second is expected start time (relative to the test start time).
                    [.5, 0],
                    [1, 0],
                ],
                // Expected total duration (in seconds).
                1,
            ],
            [
                [
                    [1, 0],
                    [1, 0],
                ],
                1,
            ],
            [
                [
                    [.8, 0],
                    [.6, 0],
                    [1.1, 0],
                    [1.4, 0],
                    [1.2, 0],
                    [.9, 0],
                ],
                1.4,
            ],
        ];
    }

    /**
     * @dataProvider processList
     */
    public function testProcessesRunInParallel(array $processList, $expectedDuration, $threshold = 0.3)
    {
        $loop = new Client(new LoopHandler());

        $start = microtime(true);
        $promises = [];

        foreach ($processList as list($sleep, $expectedStartTime)) {
            $output = md5(uniqid('', true));
            $promises[] = $loop->execute(sprintf('sleep %s && echo %s', $sleep, $output))
                ->then(function (Process $process) use ($output) {
                    $this->assertEquals($output, trim($process->getOutput()));
                }, function (ProcessFailedException $exception) {
                    $this->fail($exception->getMessage());
                });
        }

        \GuzzleHttp\Promise\all($promises)->wait();

        $duration = microtime(true) - $start;

        $this->assertGreaterThan($expectedDuration - $threshold, $duration);
        $this->assertLessThan($expectedDuration + $threshold, $duration);
    }

    public function testCallbackRunsAfterTimeout()
    {
        $threshold = 0.01;
        $loop = new Client(new LoopHandler());

        $t1Actual = $t2Actual = null;

        $t1Expected = microtime(true);
        $t2Expected = microtime(true) + 3;

        $promise1 = $loop->enqueue(function () use (&$t1Actual) {
            $t1Actual = microtime(true);
        });
        $promise2 = $loop->enqueueIn(3, function () use (&$t2Actual) {
            $t2Actual = microtime(true);
        });

        \GuzzleHttp\Promise\all([$promise1, $promise2])->wait();

        $this->assertGreaterThan($t1Expected - $threshold, $t1Actual);
        $this->assertLessThan($t1Expected + $threshold, $t1Actual);

        $this->assertGreaterThan($t2Expected - $threshold, $t2Actual);
        $this->assertLessThan($t2Expected + $threshold, $t2Actual);
    }

    public function requestList()
    {
        return [
            // Test simplest queue.
            [
                [
                    [1, 500, 0],
                    [1, 500, 500],
                ],
                1,
                1,
            ],
            // Test queue stacks up.
            [
                [
                    [1, 400, 0],
                    [1, 800, 0],
                    [1, 600, 400],
                ],
                // Expected duration of the whole queue (in seconds).
                1,
                // Throttle limit.
                2,
            ],
            // Test that different queues run independently.
            [
                [
                    [1, 500, 0],
                    [1, 1000, 0],
                    [2, 1000, 0],
                    [3, 1000, 0],
                ],
                1,
                2,
            ],
            // Test that queues stack pretty long.
            [
                [
                    [1, 500, 0],
                    [2, 500, 0],
                    [1, 500, 500],
                    [2, 500, 500],
                    [1, 500, 1000],
                    [2, 500, 1000],
                    [1, 500, 1500],
                    [2, 500, 1500],
                ],
                2,
                1,
            ],
            // Test a complex queue.
            [
                [
                    [1, 500, 0],
                    [2, 700, 0],
                    [3, 900, 0],
                    [1, 400, 0],
                    [1, 500, 400],
                    [1, 300, 500],
                    [2, 100, 0],
                    [2, 200, 100],
                ],
                .9,
                2,
            ],
            // Test wide queue.
            [
                [
                    [1, 100, 0],
                    [2, 200, 0],
                    [3, 300, 0],
                    [4, 400, 0],
                    [5, 500, 0],
                    [1, 100, 100],
                    [2, 200, 200],
                    [3, 300, 300],
                    [4, 400, 400],
                    [5, 500, 500],
                    [1, 100, 200],
                    [2, 200, 400],
                    [3, 300, 600],
                    [4, 400, 800],
                    [5, 500, 1000],
                ],
                1.5,
                1,
            ],
        ];
    }

    /**
     * @dataProvider requestList
     */
    public function testQueueLimitsNumberOfProcessingRequests(array $queueData, $expectedDuration, $throttleLimit, $threshold = 0.05)
    {
        $handler = new HandlerStack(new LoopHandler());
        $handler->push(ThrottleMiddleware::create());
        $client = new \GuzzleHttp\Client([
            'handler' => $handler,
            'base_uri' => Server::$url
        ]);
        $queueEnd = $promises = $responses = $expectedStart = [];
        foreach ($queueData as $queueItem) {
            $responses[] = new Response();
            list($queueId, $requestDuration, $expectedStartTime) = $queueItem;
            $options = [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::HEADERS => [
                    'duration' => $requestDuration,
                ],
                'throttle_id' => $queueId,
                'throttle_limit' => $throttleLimit,
            ];
            $expectedStart[$queueId] = $expectedStartTime;
            $promises[] = $client->getAsync('', $options)
                ->then(function () use ($queueId, &$queueStart, &$queueEnd) {
                    if (!isset($queueStart[$queueId])) {
                        $queueStart[$queueId] = microtime(true);
                    }
                    $queueEnd[$queueId] = microtime(true);
                });
        }
        Server::start();
        Server::enqueue($responses);
        $start = microtime(true);
        \GuzzleHttp\Promise\all($promises)->wait();
        $duration = microtime(true) - $start;

        $this->assertGreaterThan($expectedDuration - $threshold, $duration);
        $this->assertLessThan($expectedDuration + $threshold, $duration);

        foreach ($queueEnd as $i => $endedAt) {
            $duration = $endedAt - $start;
//            $this->assertGreaterThan($expectedDuration - $threshold, $endedAt - $start, "Queue #$i started too soon");
//            $this->assertLessThan($queueInfo->getExpectedDuration() + $threshold, $queueInfo->getDuration(), "Queue #$i started too late");
//
//            $this->assertGreaterThan($started + $queueInfo->getExpectedDelay() - $threshold, $queueInfo->getStartedAt(), "Queue #$i popped too early");
//            $this->assertLessThan($started + $queueInfo->getExpectedDelay() + $threshold, $queueInfo->getStartedAt(), "Queue #$i popped too late");
        }
    }
}
