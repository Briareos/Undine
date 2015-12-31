<?php

namespace Undine\Tests\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;
use GuzzleHttp\Promise;
use Undine\Guzzle\Middleware\ThrottleMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Undine\Loop\LoopHandler;

class MultiAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function queues()
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

    private function createHandler()
    {
        $handler  = HandlerStack::create(new Handler());
        $throttle = new ThrottleMiddleware();
        $handler->unshift($throttle->create(), 'throttle');

        return $handler;
    }

    /**
     * @param array $queueData
     * @param float $expectedQueueDuration
     * @param int   $throttleLimit
     * @param float $threshold
     *
     * @throws \Exception
     *
     * @dataProvider queues
     */
    public function testQueueLimitsNumberOfProcessingRequests(array $queueData, $expectedQueueDuration, $throttleLimit, $threshold = 0.05)
    {
        $promises = $responses = [];
        $queue    = new \ArrayIterator();

        $client = new Client([
            'handler' => $handler = $this->createHandler(),
        ]);

        $handler->after('http_errors', function (callable $fn) use ($queue) {
            return function (RequestInterface $request, array $options) use ($fn, $queue) {
                return $fn($request, $options)
                    ->then(function (ResponseInterface $response) use ($queue, $options) {
                        $id          = $options['__test_id'];
                        $requestedAt = (float) $response->getHeaderLine('requested-at');
                        $respondedAt = (float) $response->getHeaderLine('responded-at');
                        $queue[$id]->setStartedAt($requestedAt);
                        $queue[$id]->setEndedAt($respondedAt);
                        $queue[$id]->setResponseBody((string) $response->getBody());

                        return $response;
                    });
            };
        });

        $i = 0;

        foreach ($queueData as list($throttleId, $expectedDuration, $expectedDelay)) {
            $i++;
            $promise    = $client->requestAsync('GET', Server::$url, [
                'headers'        => [
                    'duration' => $expectedDuration,
                    'delay'    => $expectedDelay,
                    'id'       => $expectedDuration.':'.$expectedDelay,
                ],
                'throttle_id'    => $throttleId,
                'throttle_limit' => $throttleLimit,
                '__test_id'      => $i,
                'http_errors'    => true,
            ]);
            $queue[$i]  = new TestQueue($throttleId, $expectedDuration / 1000, $expectedDelay / 1000, $i === 0 ? 500 : 200);
            $promises[] = $promise;
            // Randomize status code.
            $responses[] = new Psr7\Response($i === 0 ? 500 : 200, [], Psr7\stream_for(md5(uniqid())));
        }

        Server::enqueue($responses);

        $started = microtime(true);
        Promise\all($promises)->wait();
        $ended = microtime(true);

        $this->assertGreaterThan($expectedQueueDuration - $threshold, $ended - $started);
        $this->assertLessThan($expectedQueueDuration + $threshold, $ended - $started);

        $i = 0;
        foreach ($queue as $id => $queueInfo) {
            $this->assertGreaterThan($queueInfo->getExpectedDuration() - $threshold, $queueInfo->getDuration(), "Queue #$i started too soon");
            $this->assertLessThan($queueInfo->getExpectedDuration() + $threshold, $queueInfo->getDuration(), "Queue #$i started too late");

            $this->assertGreaterThan($started + $queueInfo->getExpectedDelay() - $threshold, $queueInfo->getStartedAt(), "Queue #$i popped too early");
            $this->assertLessThan($started + $queueInfo->getExpectedDelay() + $threshold, $queueInfo->getStartedAt(), "Queue #$i popped too late");
        }
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage From error handler
     */
    public function testThrottleMiddlewareDoesNotSuppressDeepExceptions()
    {
        $client = new Client([
            'handler' => $handler = $this->createHandler(),
        ]);

        $responses = [
            new Psr7\Response(200),
            new Psr7\Response(200),
            new Psr7\Response(200),
            new Psr7\Response(500),
        ];
        Server::enqueue($responses);

        $request = function () use ($client) {
            return $client->requestAsync('GET', Server::$url, [
                'http_errors'    => true,
                'throttle_id'    => 1,
                'throttle_limit' => 1,
            ]);
        };

        Promise\all([
            $request(),
            $request(),
            $request(),
            $request(),
        ])
            ->then(null,
                function () {
                    throw new \Exception('From error handler');
                })
            ->wait();
    }

    protected function tearDown()
    {
        Server::stop();
    }
}

class TestQueue
{

    private $id;

    private $expectedDuration;

    private $expectedDelay;

    private $body;

    private $startedAt;

    private $endedAt;

    private $status;

    public function __construct($id, $expectedDuration, $expectedDelay, $status)
    {
        $this->id               = $id;
        $this->expectedDuration = $expectedDuration;
        $this->expectedDelay    = $expectedDelay;
        $this->status           = $status;
    }

    public function getStartedAt()
    {
        return $this->startedAt;
    }

    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;
    }

    public function getEndedAt()
    {
        return $this->endedAt;
    }

    public function setEndedAt($endedAt)
    {
        $this->endedAt = $endedAt;
    }

    public function getDuration()
    {
        return $this->endedAt - $this->startedAt;
    }

    public function getExpectedDuration()
    {
        return $this->expectedDuration;
    }

    public function getExpectedDelay()
    {
        return $this->expectedDelay;
    }

    public function setResponseBody($body)
    {
        $this->body = $body;
    }

    public function getResponseBody()
    {
        return $this->body;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
