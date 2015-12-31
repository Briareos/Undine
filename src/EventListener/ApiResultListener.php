<?php

namespace Undine\EventListener;

use GuzzleHttp\Promise\PromiseInterface;
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
use Undine\Api\Error\ConstraintInterface;
use Undine\Api\Error\Security\BadCredentials;
use Undine\Api\Error\Security\NotAuthenticated;
use Undine\Api\Error\Security\NotAuthorized;
use Undine\Api\Exception\CommandInvalidException;
use Undine\Api\Exception\ConstraintViolationException;
use Undine\Api\Result\ResultInterface;
use Undine\Api\Serializer\Context;
use Undine\Api\Serializer\Normalizer;
use Undine\Oxygen\Exception\InvalidBodyException;

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

        if (!$request->attributes->has('_api')) {
            return;
        }

        $result = $event->getControllerResult();
        if (!$result instanceof ResultInterface && !$result instanceof PromiseInterface) {
            throw new \RuntimeException(sprintf('API controller result must be an instance of %s or %s.', ResultInterface::class, PromiseInterface::class));
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
            $formError = $exception->getForm()->getErrors(true)->current();
            $data += [
                'error'    => $formError->getMessage(),
                'property' => $formError->getOrigin()->getName(),
            ];
        } elseif ($exception instanceof ConstraintViolationException) {
            $this->mergeConstraintData($data, $exception->getConstraint());
        } elseif ($exception instanceof UsernameNotFoundException) {
            $this->mergeConstraintData($data, new BadCredentials());
        } elseif ($exception instanceof AccessDeniedException) {
            if ($this->tokenStorage->getToken() && $this->tokenStorage->getToken()->getRoles()) {
                $this->mergeConstraintData($data, new NotAuthorized());
            } else {
                $this->mergeConstraintData($data, new NotAuthenticated());
            }
        } else {
            // Show full exceptions for now.
            return;
        }

        $response = new JsonResponse(array_merge(['ok' => false], $data), 200, ['x-status-code' => 200]);

        $event->setResponse($response);
    }

    /**
     * @param array               $data
     * @param ConstraintInterface $constraint
     */
    private function mergeConstraintData(array &$data, ConstraintInterface $constraint)
    {
        $data['error'] = $constraint->getName();
        if ($constraint->getData()) {
            $data = array_merge($data, $constraint->getData());
        }
    }
}
