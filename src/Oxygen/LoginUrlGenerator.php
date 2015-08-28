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
     * @param Site   $site
     * @param string $userUid  UID of the user that initiates the session. It is used to track individual sessions so they can be destroyed
     *                         on demand. This value is signed, so it cannot be intercepted.
     * @param null   $username User to log in as. Pass null to use the user with ID 1, which should always exist and have full privileges
     *                         (hardcoded in Drupal). This value is signed, so it cannot be intercepted.
     *
     * @return UriInterface
     */
    public function generateUrl(Site $site, $userUid, $username = null)
    {
        $requestId        = bin2hex($this->secureRandom->nextBytes(16));
        $requestExpiresAt = $this->currentTime->getTimestamp() + 86400;

        $query = [
            'oxygenRequestId'  => $requestId,
            'requestExpiresAt' => $requestExpiresAt,
            'actionName'       => 'site.login',
            'signature'        => \Undine\Functions\openssl_sign_data($site->getPrivateKey(), sprintf('%s|%d|%s|%s', $requestId, $requestExpiresAt, $userUid, (string)$username)),
            'username'         => $username,
            'userUid'          => $userUid,
        ];

        return $site->getUrl()->withQuery(\GuzzleHttp\Psr7\build_query($query));
    }
}
