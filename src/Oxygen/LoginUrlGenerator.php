<?php

namespace Undine\Oxygen;

use Psr\Http\Message\UriInterface;
use Symfony\Component\Security\Core\Util\SecureRandomInterface;
use Undine\Model\Site;

class LoginUrlGenerator
{
    /**
     * @var SecureRandomInterface
     */
    private $secureRandom;

    /**
     * @var \DateTime
     */
    private $currentTime;

    /**
     * @param SecureRandomInterface $secureRandom
     * @param \DateTime             $currentTime
     */
    public function __construct(SecureRandomInterface $secureRandom, \DateTime $currentTime)
    {
        $this->secureRandom = $secureRandom;
        $this->currentTime  = $currentTime;
    }

    /**
     * @param Site $site
     * @param null $username
     *
     * @return UriInterface
     */
    public function generateUrl(Site $site, $username = null)
    {
        $parameters = [];

        if ($username !== null) {
            $parameters['username'] = $username;
        }

        $requestId        = bin2hex($this->secureRandom->nextBytes(16));
        $requestExpiresAt = $this->currentTime->getTimestamp() + 86400;

        $query = [
            'oxygenRequestId'  => $requestId,
            'requestExpiresAt' => $requestExpiresAt,
            'actionName'       => 'site.login',
            'signature'        => \Undine\Functions\openssl_sign_data($site->getPrivateKey(), sprintf('%s|%s', $requestId, $requestExpiresAt)),
            'actionParameters' => $parameters,
        ];

        return $site->getUrl()->withQuery(\GuzzleHttp\Psr7\build_query($query));
    }
}
