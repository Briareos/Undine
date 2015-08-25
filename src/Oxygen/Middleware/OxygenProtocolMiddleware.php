<?php

namespace Undine\Oxygen\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;
use Undine\Model\Site;
use Undine\Oxygen\Action\ActionInterface;

/**
 * This class should map the fields 1:1 to Oxygen_EventListener_ProtocolListener.
 */
class OxygenProtocolMiddleware
{
    private static $schemePortMap = [
        'http'  => 80,
        'https' => 443,
    ];

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
        return function (callable $nextHandler) use($moduleVersion, $secureRandom, $currentTime, $handshakeKeyName, $handshakeKeyValue) {
            return new self($moduleVersion, $secureRandom, $currentTime, $handshakeKeyName, $handshakeKeyValue, $nextHandler);
        };
    }

    function __invoke(RequestInterface $request, array $options)
    {
        $fn = $this->nextHandler;

        if (!isset($options['oxygen_site']) || !$options['oxygen_site'] instanceof Site) {
            throw new \RuntimeException(sprintf('The option "oxygen_site" is expected to contain an instance of %s.', Site::class));
        }

        if (!isset($options['oxygen_action']) || !$options['oxygen_action'] instanceof Site) {
            throw new \RuntimeException(sprintf('The option "oxygen_action" is expected to contain an instance of %s.', ActionInterface::class));
        }

        /** @var Site $site */
        $site = $options['oxygen_site'];
        /** @var ActionInterface $action */
        $action = $options['oxygen_action'];

        $nonce = $this->generateNonce();

        $requestData = [
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

        $oxygenRequest = $request->withBody(\GuzzleHttp\Psr7\stream_for(json_encode($requestData)));

        return $fn($oxygenRequest, $options);
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

        return $signature;
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
        $port = '';
        if ($url->getPort() !== self::$schemePortMap[$url->getScheme()]) {
            $port = ':'.$url->getPort();
        }

        return sprintf('%s%s%s', $url->getHost(), $port, rtrim($url->getPath(), '/'));
    }
}
