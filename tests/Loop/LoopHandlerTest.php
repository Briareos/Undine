<?php

namespace Undine\Tests\Loop;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Undine\Loop\Client;
use Undine\Loop\LoopHandler;

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
    public function testProcessesRunInParallel(array $processList, $expectedDuration, $threshold = 0.15)
    {
        $loop = new Client(new LoopHandler());

        $start = microtime(true);
        $promises = [];

        foreach ($processList as list($sleep, $expectedStartTime)) {
            $promises[] = $loop->execute(sprintf('sleep %s ; echo %s', $sleep, md5(uniqid('', true))))
                ->then(function (Process $process) {
                    $this->assertEquals(32, strlen(trim($process->getOutput())));
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
        $loop      = new Client(new LoopHandler());

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
}
