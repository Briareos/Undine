<?php

namespace Undine\Thumbnail;

use Psr\Http\Message\UriInterface;

class CaptureConfiguration
{
    const FORMAT_JPEG = 'jpeg';

    const FORMAT_PNG = 'png';

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
     * @var int
     */
    private $captureWidth;

    /**
     * @var int
     */
    private $captureHeight;

    /**
     * @var string
     */
    private $captureFormat;

    /**
     * @var int
     */
    private $captureQuality;

    /**
     * @param UriInterface $url
     * @param int          $captureWidth
     * @param int          $captureHeight
     * @param string       $captureFormat
     * @param int          $captureQuality From 1 to 100.
     */
    public function __construct(UriInterface $url, $captureWidth, $captureHeight, $captureFormat = self::FORMAT_PNG, $captureQuality = 100)
    {
        $this->url = $url;
        $this->captureWidth = $captureWidth;
        $this->captureHeight = $captureHeight;
        $this->captureFormat = $captureFormat;
        $this->captureQuality = $captureQuality;
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
        return (bool)strlen($this->httpUsername);
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
     * @param string $username
     * @param string $password
     */
    public function setHttpCredentials($username, $password = '')
    {
        if (strlen($username) === 0) {
            throw new \InvalidArgumentException('HTTP username must be provided.');
        }
        $this->httpUsername = $username;
        $this->httpPassword = (string)$password;
    }

    public function removeHttpCredentials()
    {
        $this->httpUsername = $this->httpPassword = null;
    }

    /**
     * @return int
     */
    public function getCaptureWidth()
    {
        return $this->captureWidth;
    }

    /**
     * @return int
     */
    public function getCaptureHeight()
    {
        return $this->captureHeight;
    }

    /**
     * @return string
     */
    public function getCaptureFormat()
    {
        return $this->captureFormat;
    }

    /**
     * @return int
     */
    public function getCaptureQuality()
    {
        return $this->captureQuality;
    }
}
