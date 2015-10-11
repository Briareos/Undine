<?php

namespace Undine\EventListener;

use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Undine\Api\Progress\ProgressInterface;
use Undine\Api\Result\ResultInterface;
use Undine\Configuration\Api;
use Undine\Http\AsyncHttpKernel;
use Undine\Http\OutputFlusher;
use Undine\Http\StreamPump;

class ApiListener implements EventSubscriberInterface
{
    /**
     * @var AsyncHttpKernel
     */
    private $httpKernel;
    /**
     * @var OutputFlusher
     */
    private $outputFlusher;

    /**
     * @param AsyncHttpKernel $httpKernel
     * @param OutputFlusher   $outputFlusher
     */
    public function __construct(AsyncHttpKernel $httpKernel, OutputFlusher $outputFlusher)
    {
        $this->httpKernel    = $httpKernel;
        $this->outputFlusher = $outputFlusher;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', -200],
        ];
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->attributes->has('_api')) {
            return;
        }

        /** @var Api $api */
        $api = $request->attributes->get('_api');

        $subRequests = $this->getSubRequests($request);
        $bulk        = $api->isBulkable() && $subRequests;
        $stream      = ($api->isStreamable() || $bulk) && ($this->shouldStream($request));

        if (!$bulk && !$stream) {
            $request->attributes->set('stream', function () {
            });

            return;
        }

        if ($subRequests) {
            // This listener will unwind/spread the calls, so don't trigger other Api listeners.
            $request->attributes->remove('_api');
        }

        if ($stream) {
            $headers = ['content-type' => 'application/json; boundary=NL', 'x-accel-buffering' => 'no'];
        } else {
            $headers = ['content-type' => 'application/json'];
        }

        if ($subRequests) {
            $event->setController(function () use ($request, $headers, $subRequests, $stream) {
                return new StreamedResponse(function () use ($request, $subRequests, $stream) {
                    $noOp     = function () {
                    };
                    $promises = [];
                    foreach ($subRequests as $i => $requestParams) {
                        // Forward the query string without the 'payload', and put all the parameters in the body.
                        $query = $request->query->all();
                        if (isset($query['payload'])) {
                            unset($query['payload']);
                        }
                        $subRequest = $request->duplicate([], $requestParams);
                        // Also force-make it a POST request, so it can contain a body.
                        $subRequest->setMethod('POST');
                        $subRequest->attributes->set('stream', $stream ? $this->createStreamer($i) : $noOp);
                        /** @var PromiseInterface $promise */
                        $promises[] = $promise = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST, true, false);
                        if ($stream) {
                            $streamer = $this->createStreamer($i);
                            $promise->then(function (Response $response) use ($streamer) {
                                $streamer($response->getContent());
                            });
                        }
                    }
                    $responses = \GuzzleHttp\Promise\all($promises)->wait();
                    if (!$stream) {
                        echo '[';
                        reset($responses);
                        while ($response = current($responses)) {
                            /** @var Response $response */
                            next($responses);
                            echo $response->getContent();
                            if (current($responses)) {
                                echo ',';
                            }
                        }
                        echo ']';
                    }
                }, 200, $headers);
            });
        } else {
            $event->setController(function () use ($request, $headers) {
                return new StreamedResponse(function () use ($request) {
                    $streamer = $this->createStreamer();
                    $request->attributes->set('stream', $streamer);
                    $response = $this->httpKernel->handle($request, HttpKernelInterface::SUB_REQUEST, true, true);
                    $streamer($response->getContent());
                }, 200, $headers);
            });
        }
    }

    /**
     * @param int|null $index
     *
     * @return StreamPump
     */
    private function createStreamer($index = null)
    {
        return new StreamPump($this->outputFlusher, $index);
    }

    /**
     * Pull parameters from the "payload", if it's provided.
     *
     * @param Request $request
     *
     * @return array Request payloads
     */
    private function getSubRequests(Request $request)
    {
        if ($request->request->has('payload')) {
            $payload = $request->request->get('payload');
        } elseif ($request->request->all()) {
            $payload = $request->request->all();
        } elseif ($request->query->get('payload')) {
            $payload = $request->query->get('payload');
        } else {
            return [];
        }

        if (!is_array($payload)) {
            return [];
        }

        // The request has a body, check if it's a normal indexed array.
        $index = 0;
        foreach ($payload as $key => $value) {
            if ($key !== $index) {
                return [];
            }
            if (!is_array($value)) {
                return [];
            }
            $index++;
        }

        return $payload;
    }

    private function shouldStream(Request $request)
    {
        return (bool)$request->query->get('stream', false)
        || strpos($request->headers->get('accept'), 'application/ldjson');
    }
}
