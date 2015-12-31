<?php

namespace Undine\Drupal;

use GuzzleHttp\Cookie\CookieJarInterface;
use Undine\Model\Site\FtpCredentials;
use Undine\Model\Site\HttpCredentials;


/**
 * This class complies with RequestOptions, hence the inconsistent return values (sometimes null, other times false).
 * Just making the life easier, I hope.
 *
 * @see RequestOptions
 */
class Session
{
    /**
     * @var CookieJarInterface|null
     */
    private $cookieJar;

    /**
     * @var HttpCredentials|null
     */
    private $httpCredentials;

    /**
     * @var FtpCredentials|null
     */
    private $ftpCredentials;

    /**
     * @param CookieJarInterface|null $cookieJar
     * @param HttpCredentials|null    $httpCredentials
     * @param FtpCredentials|null     $ftpCredentials
     */
    public function __construct(CookieJarInterface $cookieJar = null, HttpCredentials $httpCredentials = null, FtpCredentials $ftpCredentials = null)
    {
        $this->cookieJar       = $cookieJar;
        $this->httpCredentials = $httpCredentials;
        $this->ftpCredentials  = $ftpCredentials;
    }

    /**
     * @return CookieJarInterface|false
     */
    public function getCookieJar()
    {
        return $this->cookieJar ?: false;
    }

    /**
     * @return string[]|null
     */
    public function getAuthData()
    {
        return ($this->httpCredentials && $this->httpCredentials->present()) ? [$this->httpCredentials->getUsername(), $this->httpCredentials->getPassword()] : null;
    }

    /**
     * @return FtpCredentials|null
     */
    public function getFtpCredentials()
    {
        return $this->ftpCredentials ?: false;
    }
}
