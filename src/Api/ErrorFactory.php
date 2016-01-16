<?php

namespace Undine\Api;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Undine\Api\Error as E;
use Undine\Api\Exception\CommandInvalidException;
use Undine\Api\Exception\ConstraintViolationException;
use Undine\Oxygen\Exception\NetworkException;
use Undine\Oxygen\Exception\OxygenException;
use Undine\Oxygen\Exception\ProtocolException;
use Undine\Oxygen\Exception\ResponseException;

/**
 * Transforms any exception into an application error for user to see.
 */
class ErrorFactory
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param LoggerInterface       $logger
     */
    public function __construct(TokenStorageInterface $tokenStorage, LoggerInterface $logger)
    {
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
    }

    public function __invoke(\Exception $exception)
    {
        $error = $this->generateErrorForException($exception);

        if ($error instanceof E\Api\UnexpectedError) {
            // Maybe emit an event?
            $this->logger->error('Unexpected exception occurred in API.', [
                'type' => 'connection',
                'class' => get_class($exception),
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
        }

        return $error;
    }

    private function generateErrorForException(\Exception $exception)
    {
        if ($exception instanceof CommandInvalidException) {
            $formError = $exception->getForm()->getErrors(true)->current();
            $path = $formError->getOrigin()->getPropertyPath();
            if ($path !== null) {
                // We got PropertyPathInterface or maybe even a string (undocumented).
                $path = (string)$path;
            }

            return new E\Api\BadRequest($formError->getMessage(), $path);
        } elseif ($exception instanceof ConstraintViolationException) {
            return $exception->getError();
        } elseif ($exception instanceof UsernameNotFoundException) {
            return new E\Security\BadCredentials();
        } elseif ($exception instanceof AccessDeniedException) {
            $token = $this->tokenStorage->getToken();
            if ($token && $this->tokenStorage->getToken()->getRoles()) {
                return new E\Security\NotAuthorized();
            } else {
                return new E\Security\NotAuthenticated();
            }
        } elseif ($exception instanceof ProtocolException) {
            return $this->getErrorForOxygenProtocolException($exception);
        } else {
            return new E\Api\UnexpectedError();
        }
    }

    /**
     * @param ProtocolException $exception
     *
     * @return E\ErrorInterface
     */
    private function getErrorForOxygenProtocolException(ProtocolException $exception)
    {
        if ($exception instanceof NetworkException) {
            // There was a network error; we did not get a full response.
            switch ($exception->getOriginalCode()) {
                case CURLE_COULDNT_RESOLVE_HOST:
                    // Hostname lookup failed.
                    return new E\Network\CanNotResolveHost();
                case CURLE_COULDNT_CONNECT:
                    return new E\Network\CanNotConnect();
                case CURLE_OPERATION_TIMEOUTED:
                    return new E\Network\TimedOut($exception->getTransferInfo()->total_time);
                case CURLE_SEND_ERROR:
                    return new E\Network\SendError();
                case CURLE_RECV_ERROR:
                    return new E\Network\ReceiveError();
                default:
                    return new E\Api\UnexpectedError();
            }
        } elseif ($exception instanceof ResponseException) {
            // We got a full response, but did not find the Oxygen module's response.
            if ($exception->getResponse()->hasHeader('www-authenticate') && $exception->getResponse()->getStatusCode() === 401) {
                // HTTP authorization encountered.
                $realm = '';
                preg_match('{^Basic realm="(.*)"$}i', $exception->getResponse()->getHeaderLine('www-authenticate'), $matches);
                if (!empty($matches[1])) {
                    $realm = $matches[1];
                }

                return new E\Response\Unauthorized($realm, $exception->getRequest()->hasHeader('authorize'));
            } else {
                // @todo: Add special handling for >=400 status codes.
                return new E\Response\OxygenNotFound();
            }
        } elseif ($exception instanceof OxygenException) {
            // We got an exception or a fatal error directly from the Oxygen module.
            $exceptionData = $exception->getExceptionData();

            // This is a fatal error or an exception.
            return new E\Oxygen\Error($exceptionData->getMessage(), $exception->getCode(), $exceptionData->getType(), $exceptionData->getFile(), $exceptionData->getLine(), $exceptionData->getTraceString());
        } else {
            // This should never ever happen.
            return new E\Api\UnexpectedError();
        }
    }
}
