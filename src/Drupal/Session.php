<?php

namespace Undine\Drupal;

use GuzzleHttp\Cookie\CookieJarInterface;


/**
 * This class complies with RequestOptions, hence the inconsistent return values (sometimes null, sometimes false).
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
     * @var string|null
     */
    private $httpUsername;

    /**
     * @var string|null
     */
    private $httpPassword;

    /**
     * @param CookieJarInterface|null $cookieJar
     * @param string|null             $httpUsername
     * @param string|null             $httpPassword
     */
    public function __construct(CookieJarInterface $cookieJar = null, $httpUsername = null, $httpPassword = null)
    {
        $this->cookieJar    = $cookieJar;
        $this->httpUsername = $httpUsername;
        $this->httpPassword = $httpPassword;
    }

    /**
     * @return CookieJarInterface|false
     */
    public function getCookieJar()
    {
        return $this->cookieJar === null ? false : $this->cookieJar;
    }

    /**
     * @return string[]|null
     */
    public function getHttpCredentials()
    {
        return strlen($this->httpUsername) ? [$this->httpUsername, $this->httpPassword] : null;
    }
}
