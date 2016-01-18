<?php

namespace Undine\Oxygen\Middleware;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Undine\Functions\Exception\JsonParseException;
use Undine\Model\Site;
use Undine\Oxygen\Action\ActionInterface;
use Undine\Oxygen\Exception\Data\TransferInfo;
use Undine\Oxygen\Exception\ResponseException;
use Undine\Oxygen\Exception\NetworkException;
use Undine\Oxygen\Exception\OxygenException;
use Undine\Oxygen\Reaction\ReactionInterface;
use Undine\Oxygen\State\SiteStateResultTracker;

/**
 * This class should map the fields 1:1 to Oxygen_EventListener_ProtocolListener.
 */
class OxygenProtocolMiddleware
{
    /**
     * @var string
     */
    private $moduleVersion;

    /**
     * @var string
     */
    private $handshakeKeyName;

    /**
     * @var string
     */
    private $handshakeKeyValue;

    /**
     * @var SiteStateResultTracker
     */
    private $stateTracker;

    /**
     * @var callable
     */
    private $nextHandler;

    /**
     * @param string $moduleVersion
     * @param string $handshakeKeyName
     * @param string $handshakeKeyValue
     * @param SiteStateResultTracker $stateTracker
     * @param callable $nextHandler
     */
    public function __construct($moduleVersion, $handshakeKeyName, $handshakeKeyValue, SiteStateResultTracker $stateTracker, callable $nextHandler)
    {
        $this->moduleVersion = $moduleVersion;
        $this->handshakeKeyName = $handshakeKeyName;
        $this->handshakeKeyValue = $handshakeKeyValue;
        $this->stateTracker = $stateTracker;
        $this->nextHandler = $nextHandler;
    }

    public static function create($moduleVersion, $handshakeKeyName, $handshakeKeyValue, SiteStateResultTracker $stateTracker)
    {
        return function (callable $nextHandler) use ($moduleVersion, $handshakeKeyName, $handshakeKeyValue, $stateTracker) {
            return new self($moduleVersion, $handshakeKeyName, $handshakeKeyValue, $stateTracker, $nextHandler);
        };
    }

    public function __invoke(RequestInterface $request, array $options)
    {
        $fn = $this->nextHandler;

        if (!isset($options['oxygen_site']) || !$options['oxygen_site'] instanceof Site) {
            throw new \RuntimeException(sprintf('The option "oxygen_site" is expected to contain an instance of %s.', Site::class));
        }

        if (!isset($options['oxygen_action']) || !$options['oxygen_action'] instanceof ActionInterface) {
            throw new \RuntimeException(sprintf('The option "oxygen_action" is expected to contain an instance of %s.', ActionInterface::class));
        }

        $transferInfo = $previousCallable = null;
        if (isset($options['on_stats'])) {
            $previousCallable = $options['on_stats'];
        }
        $options['on_stats'] = function (TransferStats $stats) use (&$transferInfo, $previousCallable) {
            $transferInfo = new TransferInfo($stats->getHandlerStats());
            if ($previousCallable) {
                /* @var callable $previousCallable */
                $previousCallable($stats);
            }
        };

        /** @var Site $site */
        $site = $options['oxygen_site'];
        /** @var ActionInterface $action */
        $action = $options['oxygen_action'];

        $options['request_id'] = $requestId = \Undine\Functions\generate_uuid();
        // Kind of like str_rot8, for hexadecimal strings.
        $responseId = strtr($requestId, 'abcdef0123456789', '23456789abcdef01');
        $expiresAt = time() + 86400;
        $userName = '';
        $stateParameters = $this->stateTracker->getParameters($site);

        $requestData = [
            // Request nonce/ID. It's also expected to be present in the response as oxygenResponseId = str_rot18(oxygenRequestId),
            // so we are absolutely certain we got a response from the module.
            'oxygenRequestId' => $requestId,
            // When the nonce should expire. There is no need for this value to be too low; the point is to persist
            // them on the module so they can be safely cleared when they expire.
            'requestExpiresAt' => $expiresAt,
            // The public key is always provided so initial request would automatically save the key.
            // There are no "connect website"/"re-connect website" requests.
            'publicKey' => $site->getPublicKey(),
            // The reason we're signing both the nonce (request ID) and the expiration time is to prevent a certain kind
            // of reply attack where one could significantly lower the expiration time and retry the request as long as
            // they would like.
            'signature' => \Undine\Functions\openssl_sign_data($site->getPrivateKey(), sprintf('%s|%d', $requestId, $expiresAt)),
            // This is used only during the initial handshake (when the website does not have a public key set).
            // The signature is double-checked against a normalized URL, so an attacker cannot save handshake requests
            // and forward them to target victim website.
            // The point is that only authorized entities can provide public keys (field 'publicKey' above).
            'handshakeKey' => $this->handshakeKeyName,
            'handshakeSignature' => \Undine\Functions\openssl_sign_data($this->handshakeKeyValue, $this->getUrlSlug($site->getUrl())),
            // The module will throw an error if its version is lower.
            'version' => $this->moduleVersion,
            // URL of the website as we know it. The website itself will reject the request if it doesn't match,
            // so it should be handled accordingly. Probably by updating the site entity and retrying the request.
            'baseUrl' => (string)$site->getUrl(),
            // Action name and action parameters are pretty similar to Symfony's concept of actions.
            // Parameters are also automatically ordered using reflection to match method's signature.
            'actionName' => $action->getName(),
            'actionParameters' => $action->getParameters(),
            // This doesn't have use currently, but it's implemented at protocol level for statistic purposes.
            'userName' => $userName,
            // Implemented to tie in dashboard users to site administrators, also for statistic purposes.
            // Eg. when expiring one-time-login link sessions.
            'userId' => $site->getUser()->getId(),
            // Each action, beside its response, returns site's "state". The parameters passed here are mostly regarding
            // the current state info we have, eg. the checksum of the table that stores available updates.
            'stateParameters' => $stateParameters,
        ];

        $oxygenRequest = $request
            ->withHeader('accept', 'text/html,application/json,application/oxygen')
            ->withBody(\GuzzleHttp\Psr7\stream_for(json_encode($requestData)));

        if ($site->hasHttpCredentials()) {
            $oxygenRequest = $oxygenRequest->withHeader('Authorization', 'Basic ' . base64_encode(sprintf('%s:%s', $site->getHttpCredentials()->getUsername(), $site->getHttpCredentials()->getPassword())));
        }

        return $fn($oxygenRequest, $options)
            ->then(
                function (ResponseInterface $response) use ($site, $request, $options, &$transferInfo, $responseId, $action) {
                    $responseData = $this->extractData($responseId, $request, $options, $response, $transferInfo);
                    try {
                        $reaction = $this->createReaction($action, $responseData);
                    } catch (ExceptionInterface $e) {
                        throw new ResponseException(ResponseException::ACTION_RESULT_MALFORMED, $request, $options, $response, $transferInfo, $e);
                    }
                    try {
                        $this->stateTracker->setResult($site, $responseData['stateResult']);
                    } catch (\Exception $e) {
                        throw new ResponseException(ResponseException::STATE_MALFORMED, $request, $options, $response, $transferInfo, $e);
                    }

                    return $reaction;
                },
                function (RequestException $e) use ($options, &$transferInfo) {
                    throw new NetworkException($e->getHandlerContext()['errno'], $e->getRequest(), $options, $e->getResponse(), $transferInfo);
                }
            )
            ->otherwise(
                function (\Exception $exception) use ($site) {
                    $this->stateTracker->setException($site, $exception);

                    throw $exception;
                });
    }

    /**
     * @param ActionInterface $action
     * @param array $data
     *
     * @return ReactionInterface
     *
     * @throws \RuntimeException  If the reaction class cannot be found.
     * @throws ExceptionInterface If the reaction data is malformed.
     */
    private function createReaction(ActionInterface $action, array $data)
    {
        $reactionClass = $action->getReactionClass();
        if (!class_exists($reactionClass)) {
            throw new \RuntimeException(sprintf('The reaction class "%s" for action "%s" could not be found.', $reactionClass, get_class($action)));
        }
        /** @var ReactionInterface $reaction */
        $reaction = new $reactionClass();
        $reaction->setData($data['actionResult']);

        return $reaction;
    }

    /**
     * Must work exactly as Oxygen_Util::getUrlSlug, or unexpected behaviour might occur.
     *
     * @param UriInterface $url
     *
     * @return string
     *
     * @see Oxygen_Util::getUrlSlug
     */
    private function getUrlSlug(UriInterface $url)
    {
        return sprintf('%s%s%s', $url->getHost(), ($url->getPort() ? ':' . $url->getPort() : ''), rtrim($url->getPath(), '/'));
    }

    /**
     * Finds a JSON line that should be our response.
     * Should handle edge-cases where the Oxygen module's output is polluted prematurely (by PHP errors)
     * or in the shutdown context (by register_shutdown_function output).
     *
     * @param string $responseId
     * @param RequestInterface $request
     * @param array $requestOptions
     * @param ResponseInterface $response
     * @param TransferInfo $transferInfo
     *
     * @return array
     *
     * @throws OxygenException
     * @throws ResponseException
     */
    private function extractData($responseId, RequestInterface $request, array $requestOptions, ResponseInterface $response, TransferInfo $transferInfo)
    {
        $createException = function ($code, \Exception $previous = null) use ($request, $requestOptions, $response, $transferInfo) {
            throw new ResponseException($code, $request, $requestOptions, $response, $transferInfo, $previous);
        };
        if ($response->getBody()->getSize() > 10 * 1024 * 1024) {
            // Safe-guard; don't parse the body if it's larger than 10MB.
            throw $createException(ResponseException::BODY_TOO_LARGE);
        }
        // Find all lines that might represent a JSON string.
        $matchFound = preg_match(sprintf('{^({"oxygenResponseId":"%s",.*?})\s?$}m', preg_quote($responseId)), (string)$response->getBody(), $matches);

        if (!$matchFound) {
            throw $createException(ResponseException::RESPONSE_NOT_FOUND);
        }

        try {
            $data = \Undine\Functions\json_parse($matches[1]);
        } catch (JsonParseException $e) {
            throw $createException(ResponseException::RESPONSE_INVALID_JSON);
        }

        // Our response should always resolve to an array.
        if (!is_array($data)) {
            throw $createException(ResponseException::RESPONSE_NOT_AN_ARRAY);
        }

        if (isset($data['exception'])) {
            if (!is_array($data['exception'])) {
                throw $createException(ResponseException::EXCEPTION_NOT_ARRAY);
            }

            try {
                throw OxygenException::createFromData($data['exception'], $request, $requestOptions, $response, $transferInfo);
            } catch (ExceptionInterface $e) {
                throw $createException(ResponseException::MALFORMED_EXCEPTION, $e);
            }
        } elseif (isset($data['actionResult'])) {
            if (!is_array($data['actionResult'])) {
                throw $createException(ResponseException::ACTION_RESULT_NOT_ARRAY);
            }
            if (!isset($data['stateResult'])) {
                throw $createException(ResponseException::STATE_EMPTY);
            }
            if (!is_array($data['stateResult'])) {
                throw $createException(ResponseException::STATE_NOT_ARRAY);
            }

            return $data;
        }

        throw $createException(ResponseException::RESULT_NOT_FOUND, $request, $response, $transferInfo);
    }
}
