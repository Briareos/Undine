<?php

namespace Undine\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Undine\Api\Constraint\SecurityConstraint;
use Undine\Api\Exception\CommandInvalidException;
use Undine\Api\Exception\ConstraintViolationException;
use Undine\Api\Result\ResultInterface;
use Undine\Api\Serializer\Context;
use Undine\Api\Serializer\Normalizer;

class ApiResultListener implements EventSubscriberInterface
{
    private $normalizer;

    private $requestMatcher;

    private $tokenStorage;

    public function __construct(Normalizer $normalizer, RequestMatcherInterface $requestMatcher, TokenStorage $tokenStorage)
    {
        $this->normalizer     = $normalizer;
        $this->requestMatcher = $requestMatcher;
        $this->tokenStorage   = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW      => ['onKernelView', -255],
            KernelEvents::EXCEPTION => ['onKernelException', 1],
        ];
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('_api_result')) {
            return;
        }

        $result = $event->getControllerResult() ?: [];
        if (!$result instanceof ResultInterface) {
            throw new \RuntimeException(sprintf('API controller result must be an instance of %s.', ResultInterface::class));
        }

        $includes = $request->get('include', null);
        $context  = new Context(is_scalar($includes) ? $includes : '');

        // Make the 'ok' property first.
        $result = array_merge(['ok' => true], $result->normalize($this->normalizer, $context));
        $data   = json_encode($result);

        $response = new Response($data, 200, ['content-type' => 'application/json']);

        $event->setResponse($response);
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$event->getRequest()->attributes->has('_api_result') && !$this->requestMatcher->matches($event->getRequest())) {
            return;
        }

        $exception = $event->getException();

        $data = [];

        if ($exception instanceof CommandInvalidException) {
            $data += ['error' => $exception->getForm()->getErrors(true)->current()->getMessage()];
        } elseif ($exception instanceof ConstraintViolationException) {
            $data += ['error' => $exception->getConstraintId()];
        } elseif ($exception instanceof UsernameNotFoundException) {
            $data += ['error' => SecurityConstraint::BAD_CREDENTIALS];
        } elseif ($exception instanceof AccessDeniedException) {
            if ($this->tokenStorage->getToken() && $this->tokenStorage->getToken()->getRoles()) {
                $data += ['error' => SecurityConstraint::NOT_AUTHORIZED];
            } else {
                $data += ['error' => SecurityConstraint::NOT_AUTHENTICATED];
            }
        } else {
            return;
            $data += ['error' => $exception->getMessage()];
        }

        $response = new JsonResponse(array_merge(['ok' => false], $data), 200, ['x-status-code' => 200]);

        $event->setResponse($response);
    }
}
