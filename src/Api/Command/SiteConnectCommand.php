<?php

namespace Undine\Api\Command;

use Psr\Http\Message\UriInterface;
use Undine\Model\Site\AdminCredentials;
use Undine\Model\Site\FtpCredentials;
use Undine\Model\Site\HttpCredentials;

class SiteConnectCommand extends AbstractCommand
{
    /**
     * @var UriInterface
     */
    private $url;

    /**
     * @var bool
     */
    private $checkUrl;

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
     * @var string|null
     */
    private $ftpMethod;

    /**
     * @var string|null
     */
    private $ftpUsername;

    /**
     * @var string|null
     */
    private $ftpPassword;
    /**
     * @var string|null
     */
    private $ftpHost;
    /**
     * @var int|null
     */
    private $ftpPort;

    /**
     * @param UriInterface $url
     * @param bool         $checkUrl
     * @param string|null  $httpUsername
     * @param string|null  $httpPassword
     * @param string|null  $adminUsername
     * @param string|null  $adminPassword
     * @param string|null  $ftpMethod
     * @param string|null  $ftpUsername
     * @param string|null  $ftpPassword
     * @param string|null  $ftpHost
     * @param int|null     $ftpPort
     */
    public function __construct($url, $checkUrl = false, $httpUsername = null, $httpPassword = null, $adminUsername = null, $adminPassword = null, $ftpMethod = null, $ftpUsername = null, $ftpPassword = null, $ftpHost = null, $ftpPort = null)
    {
        // These properties are set by the Form component through reflection, that's why we can't initialize all the properties here.
        $this->url = $url;
        $this->checkUrl = $checkUrl;
        $this->httpUsername = $httpUsername;
        $this->httpPassword = $httpPassword;
        $this->adminUsername = $adminUsername;
        $this->adminPassword = $adminPassword;
        $this->ftpMethod = $ftpMethod;
        $this->ftpUsername = $ftpUsername;
        $this->ftpPassword = $ftpPassword;
        $this->ftpHost = $ftpHost;
        $this->ftpPort = $ftpPort;
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
    public function checkUrl()
    {
        return $this->checkUrl;
    }

    /**
     * @return bool
     */
    public function hasHttpCredentials()
    {
        return (bool)strlen($this->httpUsername);
    }

    /**
     * @return HttpCredentials
     */
    public function getHttpCredentials()
    {
        $credentials = new HttpCredentials();
        if ($this->hasHttpCredentials()) {
            $credentials->set($this->httpUsername, $this->httpPassword);
        }

        return $credentials;
    }

    /**
     * @return bool
     */
    public function hasAdminCredentials()
    {
        return strlen($this->adminUsername) && strlen($this->adminPassword);
    }

    /**
     * @return AdminCredentials
     */
    public function getAdminCredentials()
    {
        $credentials = new AdminCredentials();
        if ($this->hasAdminCredentials()) {
            $credentials->set($this->adminUsername, $this->adminPassword);
        }

        return $credentials;
    }

    /**
     * @return bool
     */
    public function hasFtpCredentials()
    {
        return strlen($this->ftpMethod) && strlen($this->ftpUsername);
    }

    /**
     * @return FtpCredentials
     */
    public function getFtpCredentials()
    {
        $credentials = new FtpCredentials();
        if ($this->hasFtpCredentials()) {
            $credentials->set($this->ftpMethod, $this->ftpUsername, $this->ftpPassword, $this->ftpHost, $this->ftpPort);
        }

        return $credentials;
    }

    /**
     * @internal
     *
     * @return string|null
     */
    public function getHttpUsername()
    {
        return $this->httpUsername;
    }

    /**
     * @internal
     *
     * @return string|null
     */
    public function getHttpPassword()
    {
        return $this->httpPassword;
    }

    /**
     * @internal
     *
     * @return string|null
     */
    public function getAdminUsername()
    {
        return $this->adminUsername;
    }

    /**
     * @internal
     *
     * @return string|null
     */
    public function getAdminPassword()
    {
        return $this->adminPassword;
    }

    /**
     * @internal
     *
     * @return string|null
     */
    public function getFtpMethod()
    {
        return $this->ftpMethod;
    }

    /**
     * @internal
     *
     * @return string|null
     */
    public function getFtpUsername()
    {
        return $this->ftpUsername;
    }

    /**
     * @internal
     *
     * @return string|null
     */
    public function getFtpPassword()
    {
        return $this->ftpPassword;
    }

    /**
     * @internal
     *
     * @return string|null
     */
    public function getFtpHost()
    {
        return $this->ftpHost;
    }

    /**
     * @internal
     *
     * @return int|null
     */
    public function getFtpPort()
    {
        return $this->ftpPort;
    }
}
