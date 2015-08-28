<?php

namespace Undine\Oxygen\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;
use Undine\Functions\Exception\JsonParseException;
use Undine\Model\Site;
use Undine\Oxygen\Action\ActionInterface;
use Undine\Oxygen\Exception\InvalidBodyException;
use Undine\Oxygen\Exception\InvalidContentTypeException;
use Undine\Oxygen\Exception\OxygenException;
use Undine\Oxygen\Reaction\ReactionInterface;

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
     * @var SecureRandomInterface
     */
    private $secureRandom;

    /**
     * @var \DateTime
     */
    private $currentTime;

    /**
     * @var string
     */
    private $handshakeKeyName;

    /**
     * @var string
     */
    private $handshakeKeyValue;

    /**
     * @var callable
     */
    private $nextHandler;

    /**
     * @param string                $moduleVersion
     * @param SecureRandomInterface $secureRandom
     * @param \DateTime             $currentTime
     * @param string                $handshakeKeyName
     * @param string                $handshakeKeyValue
     * @param callable              $nextHandler
     */
    public function __construct($moduleVersion, SecureRandomInterface $secureRandom, \DateTime $currentTime, $handshakeKeyName, $handshakeKeyValue, callable $nextHandler)
    {
        $this->moduleVersion     = $moduleVersion;
        $this->secureRandom      = $secureRandom;
        $this->currentTime       = $currentTime;
        $this->handshakeKeyName  = $handshakeKeyName;
        $this->handshakeKeyValue = $handshakeKeyValue;
        $this->nextHandler       = $nextHandler;
    }

    public static function create($moduleVersion, SecureRandomInterface $secureRandom, \DateTime $currentTime, $handshakeKeyName, $handshakeKeyValue)
    {
        return function (callable $nextHandler) use ($moduleVersion, $secureRandom, $currentTime, $handshakeKeyName, $handshakeKeyValue) {
            return new self($moduleVersion, $secureRandom, $currentTime, $handshakeKeyName, $handshakeKeyValue, $nextHandler);
        };
    }

    function __invoke(RequestInterface $request, array $options)
    {
        $fn = $this->nextHandler;

        if (!isset($options['oxygen_site']) || !$options['oxygen_site'] instanceof Site) {
            throw new \RuntimeException(sprintf('The option "oxygen_site" is expected to contain an instance of %s.', Site::class));
        }

        if (!isset($options['oxygen_action']) || !$options['oxygen_action'] instanceof ActionInterface) {
            throw new \RuntimeException(sprintf('The option "oxygen_action" is expected to contain an instance of %s.', ActionInterface::class));
        }

        /** @var Site $site */
        $site = $options['oxygen_site'];
        /** @var ActionInterface $action */
        $action = $options['oxygen_action'];

        $requestId = bin2hex($this->secureRandom->nextBytes(16));
        $expiresAt = $this->currentTime->getTimestamp() + 86400;
        $username = '';

        $requestData = [
            // Request nonce/ID. It's also expected to be present in the response, so we know we
            // got a response from the module.
            'oxygenRequestId'    => $requestId,
            // When the nonce should expire. There is no need for this value to be too low; the point is to persist
            // them on the module so they can be safely cleared when they expire.
            'requestExpiresAt'   => $expiresAt,
            // The public key is always provided so initial request would automatically save the key.
            // There are no "connect website"/"re-connect website" requests.
            'publicKey'          => $site->getPublicKey(),
            // The reason we're signing both the nonce (request ID) and the expiration time is to prevent a certain kind
            // of reply attack where one could significantly lower the expiration time and retry the request as long as
            // they would like.
            'signature'          => \Undine\Functions\openssl_sign_data($site->getPrivateKey(), sprintf('%s|%d', $requestId, $expiresAt)),
            // This is used only during the initial handshake (when the website does not have a public key set).
            // The signature is double-checked against a normalized URL, so an attacker cannot save handshake requests
            // and forward them to target victim website.
            // The point is that only authorized entities can provide public keys (field 'publicKey' above).
            'handshakeKey'       => $this->handshakeKeyName,
            'handshakeSignature' => \Undine\Functions\openssl_sign_data($this->handshakeKeyValue, $this->getUrlSlug($site->getUrl())),
            // The module will throw an error if the required version is not met.
            'requiredVersion'    => $this->moduleVersion,
            // URL of the website as we know it. The website itself will reject the request if it doesn't match,
            // so it should be handled accordingly. Probably by updating the site entity and retrying the request.
            'baseUrl'            => (string)$site->getUrl(),
            // Action name and action parameters are pretty similar to Symfony's concept of actions.
            // Parameters are also automatically ordered using reflection to match method's signature.
            'actionName'         => $action->getName(),
            'actionParameters'   => $action->getParameters(),
            // This doesn't have use currently, but it's implemented at protocol level for statistic purposes.
            'username'           => $username,
            // Implemented to tie in dashboard users and site administrators, also for statistic purposes.
            // In LoginUrlGenerator it has more significant use.
            'userUid'            => $site->getUser()->getUid(),
        ];

        $oxygenRequest = $request
            ->withHeader('accept', 'text/html,application/json,application/oxygen')
            ->withBody(\GuzzleHttp\Psr7\stream_for(json_encode($requestData)));

        return $fn($oxygenRequest, $options)
            ->then(function (ResponseInterface $response) use ($request, $options, $requestId, $action) {
                $contentType = $response->getHeaderLine('content-type');

                if (!preg_match('{^application/json($|;)$}', $contentType)) {
                    throw new InvalidContentTypeException('application/json', $contentType, $request, $response, $options);
                }

                try {
                    $data = \Undine\Functions\json_parse($response->getBody());
                } catch (JsonParseException $e) {
                    throw new InvalidBodyException('The body provided is not valid JSON.', $request, $response, $e, $options);
                }

                if (!is_array($data)) {
                    throw new InvalidBodyException(sprintf('The JSON response must resolve to an array; got %s.', gettype($data)), $request, $response);
                }

                $this->validateData($request, $requestId, $response, $data, $options);

                $reaction = $this->createReaction($action, $data);

                return $reaction;
            });
    }

    /**
     * @param RequestInterface  $request
     * @param string            $requestId
     * @param ResponseInterface $response
     * @param array             $data
     * @param array             $options
     */
    private function validateData(RequestInterface $request, $requestId, ResponseInterface $response, array $data, array $options)
    {
        if (isset($data['oxygenException'])) {
            throw OxygenException::createFromResponseData('oxygenException', $data['oxygenException'], $request, $response, $options);
        }

        if (!isset($data['actionResult'])
            || !is_array($data['actionResult'])
            || !isset($data['oxygenResponseId'])
            || $data['oxygenResponseId'] !== $requestId
        ) {
            throw new InvalidBodyException('Unexpected response gotten', $request, $response, null, $options);
        }
    }

    /**
     * @param ActionInterface $action
     * @param array           $data
     *
     * @return ReactionInterface
     */
    private function createReaction(ActionInterface $action, array $data)
    {
        $reactionClass = $action->getReactionClass();
        if (!class_exists($reactionClass)) {
            throw new \RuntimeException(sprintf('The reaction class "%s" for action "%s" could not be found.', $reactionClass, get_class($action)));
        }
        /** @var ReactionInterface $reaction */
        $reaction = new $reactionClass();
        $resolver = new OptionsResolver();
        $reaction->configureOptions($resolver);
        $parsedData = $resolver->resolve($data['actionResult']);
        $reaction->setData($parsedData);

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
        return sprintf('%s%s%s', $url->getHost(), ($url->getPort() ? ':'.$url->getPort() : ''), rtrim($url->getPath(), '/'));
    }
}
