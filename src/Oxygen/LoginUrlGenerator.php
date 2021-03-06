<?php

namespace Undine\Oxygen;

use Psr\Http\Message\UriInterface;
use Undine\Model\Site;

class LoginUrlGenerator
{
    /**
     * @param Site        $site
     * @param string      $userId   ID of the user that initiates the session. It is used to track individual sessions so they can be destroyed
     *                              on demand. This value is signed, so it cannot be intercepted.
     * @param string|null $userName User to log in as. Pass null to use the user with ID 1, which should always exist and have full privileges
     *                              (hardcoded in Drupal). This value is signed, so it cannot be intercepted.
     *
     * @return UriInterface
     */
    public function generateUrl(Site $site, $userId, $userName = null)
    {
        $requestId = bin2hex(random_bytes(16));
        $requestExpiresAt = time() + 86400;

        $query = [
            'oxygenRequestId' => $requestId,
            'requestExpiresAt' => $requestExpiresAt,
            'actionName' => 'site.login',
            'signature' => \Undine\Functions\openssl_sign_data($site->getPrivateKey(), sprintf('%s|%d|%s|%s', $requestId, $requestExpiresAt, $userId, (string)$userName)),
            'userName' => $userName,
            'userId' => $userId,
        ];

        $url = $site->getUrl();
        if ($site->hasHttpCredentials()) {
            $url = $url->withUserInfo($site->getHttpCredentials()->getUsername(), $site->getHttpCredentials()->getPassword());
        }

        return $url->withQuery(\GuzzleHttp\Psr7\build_query($query));
    }
}
