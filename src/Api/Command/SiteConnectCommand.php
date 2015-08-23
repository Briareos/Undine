<?php

namespace Undine\Api\Command;

use Psr\Http\Message\UriInterface;

class SiteConnectCommand extends AbstractCommand
{
    /**
     * @var UriInterface
     */
    private $url;

    /**
     * @var string|null
     */
    private $httpUsername;

    /**
     * @var string|null
     */
    private $httpPassword;

    /**
     * @var string|null
     */
    private $adminUsername;

    /**
     * @var string|null
     */
    private $adminPassword;

    /**
     * @param UriInterface $url
     * @param string|null  $httpUsername
     * @param string|null  $httpPassword
     * @param string|null  $adminUsername
     * @param string|null  $adminPassword
     */
    public function __construct($url, $httpUsername = null, $httpPassword = null, $adminUsername = null, $adminPassword = null)
    {
        $this->url           = $url;
        $this->httpUsername  = $httpUsername;
        $this->httpPassword  = $httpPassword;
        $this->adminUsername = $adminUsername;
        $this->adminPassword = $adminPassword;
    }

    /**
     * @return UriInterface
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function hasHttpCredentials()
    {
        return strlen($this->httpUsername) && strlen($this->httpPassword);
    }

    /**
     * @return null|string
     */
    public function getHttpUsername()
    {
        return $this->httpUsername;
    }

    /**
     * @return null|string
     */
    public function getHttpPassword()
    {
        return $this->httpPassword;
    }

    /**
     * @return bool
     */
    public function hasAdminCredentials()
    {
        return strlen($this->adminUsername) && strlen($this->adminPassword);
    }

    /**
     * @return null|string
     */
    public function getAdminUsername()
    {
        return $this->adminUsername;
    }

    /**
     * @return null|string
     */
    public function getAdminPassword()
    {
        return $this->adminPassword;
    }
}
