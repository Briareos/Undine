<?php

namespace Undine\Oxygen\Middleware;

use GuzzleHttp\Exception\RequestException;
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
     * @var OptionsResolver[]
     */
    private $cachedResolvers = [];

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

        $nonce = $this->generateNonce();

        $requestId = bin2hex($this->secureRandom->nextBytes(20));

        $requestData = [
            'oxygenRequestId'    => $requestId,
            'nonce'              => $nonce,
            'publicKey'          => $site->getPublicKey(),
            'signature'          => $this->sign($site->getPrivateKey(), $nonce),
            'handshakeKey'       => $this->handshakeKeyName,
            'handshakeSignature' => $this->sign($this->handshakeKeyValue, $this->getUrlSlug($site->getUrl())),
            'requiredVersion'    => $this->moduleVersion,
            'baseUrl'            => (string)$site->getUrl(),
            'actionName'         => $action->getName(),
            'actionParameters'   => $action->getParameters(),
        ];

        $oxygenRequest = $request
            ->withHeader('accept', 'text/html,application/oxygen')
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

                $this->validateData($requestId, $data);

                $reaction = $this->createReaction($action, $data);

                return $reaction;
            });
    }

    /**
     * @param string $requestId
     * @param array  $data
     */
    private function validateData($requestId, array $data)
    {
        if (!is_array($data)
            || !isset($data['actionResult'])
            || !is_array($data['actionResult'])
            || !isset($data['oxygenResponseId'])
            || $data['oxygenResponseId'] !== $requestId
        ) {
            throw new RequestException('Unexpected response gotten', $request, $response);
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
     * @return string
     */
    private function generateNonce()
    {
        return sprintf('%s_%d', bin2hex($this->secureRandom->nextBytes(16)), $this->currentTime->getTimestamp() + 86400);
    }

    /**
     * @param string $privateKey
     * @param string $data
     *
     * @return string Base64-encoded signature.
     */
    private function sign($privateKey, $data)
    {
        $signed = @openssl_sign($data, $signature, $privateKey);

        if (!$signed) {
            $lastError    = error_get_last();
            $opensslError = '';

            while (($opensslErrorRow = openssl_error_string()) !== false) {
                $opensslError = $opensslErrorRow."\n".$opensslError;
            }

            throw new \RuntimeException(sprintf('Failed to sign data using private key; last error: %s; OpenSSL error: %s', $lastError['message'], $opensslError));
        }

        return base64_encode($signature);
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
