<?php

namespace Undine\Oxygen\Middleware;

use Monolog\Logger;
use Psr\Http\Message\RequestInterface;
use Undine\Api\Error\Api\UnexpectedError;
use Undine\Api\Error\Network\CanNotResolveHost;
use Undine\Api\Error\Network\CouldNotConnect;
use Undine\Api\Error\Network\ReceiveError;
use Undine\Api\Error\Network\SendError;
use Undine\Api\Error\Network\TimedOut;
use Undine\Api\Error\Response\OxygenNotFound;
use Undine\Api\Error\Response\UnauthorizedConstraint;
use Undine\Oxygen\Exception\ConnectionException;
use Undine\Oxygen\Exception\InvalidBodyException;
use Undine\Oxygen\Exception\ResponseException;

/**
 * Transforms all client exceptions into constraints.
 * This class has the potential to become huge once it handles more complex cases,
 * but such is life. Don't split it into modules prematurely.
 */
class ClientConstraintMiddleware
{
    /**
     * @var callable
     */
    private $nextHandler;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param callable $nextHandler
     * @param Logger   $logger
     */
    public function __construct(callable $nextHandler, Logger $logger)
    {
        $this->nextHandler = $nextHandler;
        $this->logger      = $logger;
    }

    /**
     * @param Logger $logger
     *
     * @return \Closure
     */
    public function create(Logger $logger)
    {
        return function (callable $nextHandler) use ($logger) {
            return new self($nextHandler, $logger);
        };
    }

    public function __invoke(RequestInterface $request, array $options)
    {
        $fn = $this->nextHandler;

        return $fn($request, $options)
            ->otherwise(
                function (\Exception $exception) {
                    // Expected exceptions here are subclasses of ProtocolException.

                }
            );
    }

    private function createConstraint(\Exception $exception)
    {
        if ($exception instanceof ConnectionException) {
            // There was a network error; we did not get a full response.
            switch ($exception->getCode()) {
                case CURLE_COULDNT_RESOLVE_HOST:
                    // Hostname lookup failed.
                    return new CanNotResolveHost();
                case CURLE_COULDNT_CONNECT:
                    return new CouldNotConnect();
                case CURLE_OPERATION_TIMEOUTED:
                    return new TimedOut($exception->getTransferInfo()->total_time);
                case CURLE_SEND_ERROR:
                    return new SendError();
                case CURLE_RECV_ERROR:
                    return new ReceiveError();
                default:
                    $this->logUnexpectedException($exception);

                    return new UnexpectedError();
            }
        } elseif ($exception instanceof InvalidBodyException) {
            // We got a full response, but did not find the Oxygen module's response.
            if ($exception->getResponse()->hasHeader('www-authenticate') && $exception->getResponse()->getStatusCode() === 401) {
                // HTTP authorization encountered.
                $realm = '';
                preg_match('{^Basic realm="(.*)"$}i', $exception->getResponse()->getHeaderLine('www-authenticate'), $matches);
                if (!empty($matches[1])) {
                    $realm = $matches[1];
                }

                return new UnauthorizedConstraint($realm, $exception->getRequest()->hasHeader('authorize'));
            } elseif ($exception->getResponse()->getStatusCode() !== 200) {
                return new InvalidHttpStatusCodeConstraint($exception->getResponse()->getStatusCode());
            } else {
                return new OxygenNotFound();
            }
        } elseif ($exception instanceof ResponseException) {
            // We got an exception or a fatal error directly from the Oxygen module.
            $exceptionData = $exception->getExceptionData();

            // This is a fatal error.
            return new ErrorConstraint($exceptionData->getMessage(), $exceptionData->getType(), $exceptionData->getFile(), $exceptionData->getLine());
        } else {
            // Let the ApiResultListener pick this up as an unexpected exception and log it.
            return new UnexpectedError($exception);
        }
    }

    private function logUnexpectedException(\Exception $exception)
    {
        $this->logger->notice('Unexpected exception occurred in middleware.', [
            'type'    => 'connection',
            'class'   => get_class($exception),
            'code'    => $exception->getCode(),
            'message' => $exception->getMessage(),
        ]);
    }
}