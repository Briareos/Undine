<?php

namespace Undine\EventListener;

use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Undine\Api\Error\ErrorInterface;
use Undine\Api\Result\ResultInterface;
use Undine\Api\Serializer\Context;
use Undine\Api\Serializer\Normalizer;

class ApiResultListener implements EventSubscriberInterface
{
    /**
     * @var Normalizer
     */
    private $normalizer;

    /**
     * @var RequestMatcherInterface
     */
    private $requestMatcher;

    /**
     * @var callable
     */
    private $errorFactory;

    public function __construct(Normalizer $normalizer, RequestMatcherInterface $requestMatcher, callable $errorFactory)
    {
        $this->normalizer = $normalizer;
        $this->requestMatcher = $requestMatcher;
        $this->errorFactory = $errorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -255],
            KernelEvents::VIEW => ['onKernelView', -255],
            KernelEvents::EXCEPTION => ['onKernelException', 1],
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Move the 'include' query parameter to attributes, so it's not visible to the rest of the application.
        if ($include = $request->query->get('include')) {
            $request->attributes->set('include', $include);
            $request->query->remove('include');
        }
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('_api')) {
            return;
        }

        $result = $event->getControllerResult();
        if (!$result instanceof ResultInterface && !$result instanceof PromiseInterface) {
            throw new \RuntimeException(sprintf(
                'API controller result must be an instance of %s or %s, got %s.',
                ResultInterface::class,
                PromiseInterface::class,
                is_object($result) ? get_class($result) : json_encode($result)
            ));
        }

        $includes = $request->attributes->get('include', null);
        $context = new Context(is_scalar($includes) ? $includes : '');

        // Make the 'ok' property first.
        $result = array_merge(['ok' => true], $result->normalize($this->normalizer, $context));
        $data = json_encode($result);

        $response = new Response($data, 200, ['content-type' => 'application/json']);

        $event->setResponse($response);
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$event->getRequest()->attributes->has('_api_result') && !$this->requestMatcher->matches($event->getRequest())) {
            return;
        }

        $errorFactory = $this->errorFactory;
        /** @var ErrorInterface $error */
        $error = $errorFactory($event->getException());
        // Tell Symfony to not use 500 as status code.
        $response = new JsonResponse(
            array_merge(
                [
                    'ok' => false,
                    'error' => $error->getName(),
                ],
                $error->getData()
            ),
            200,
            ['x-status-code' => 200]);

        $event->setResponse($response);
    }

    /**
     * @param array $data
     * @param ErrorInterface $constraint
     */
    private function mergeConstraintData(array &$data, ErrorInterface $constraint)
    {
        $data['error'] = $constraint->getName();
        if ($constraint->getData()) {
            $data = array_merge($data, $constraint->getData());
        }
    }
}
