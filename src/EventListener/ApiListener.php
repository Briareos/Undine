<?php

namespace Undine\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Undine\Configuration\Api;

class ApiListener implements EventSubscriberInterface
{
    /**
     * @var HttpKernelInterface
     */
    private $httpKernel;

    /**
     * @param HttpKernelInterface $httpKernel
     */
    public function __construct(HttpKernelInterface $httpKernel)
    {
        $this->httpKernel = $httpKernel;
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

        if ($api->isBulkable()) {
            return;
        }

        if ($this->shouldStream($request)) {
            $event->setController(function () use ($request) {
                return new StreamedResponse(function () use ($request) {
                    if ($bulkCalls = $this->getSubRequests($request)) {
                        $responses = [];
                        foreach ($bulkCalls as $bulkCall) {
                            $subRequest  = $request->duplicate(null, $bulkCall);
                            $responses[] = $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
                        }
                        \GuzzleHttp\Promise\queue()->run();
                        foreach ($responses as $response) {

                        }
                    }
                }, 200, ['content-type' => 'application/octet-stream', 'x-accel-buffering' => 'no']);
            });
        } elseif ($bulkCalls = $this->getSubRequests($request)) {
            $event->setController(function () use ($request, $bulkCalls) {
                return new StreamedResponse(function () use ($request, $bulkCalls) {
                    $responses = [];
                    foreach ($bulkCalls as $bulkCall) {
                        $subRequest = $request->duplicate(null, $bulkCall);
                        $subRequest->setMethod('POST');
                        $responses[] = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST, true);
                    }
                    \GuzzleHttp\Promise\queue()->run();
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
                });
            });
        }
    }

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
            $index++;
        }

        return $payload;
    }

    private function shouldStream(Request $request)
    {
        return (bool)$request->query->get('stream', false);
    }
}
