<?php

namespace Undine\Repository\SiteData;

class SiteForThumbnailUpdate
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $httpUsername;

    /**
     * @var string
     */
    private $httpPassword;

    /**
     * @var string|null
     */
    private $thumbnailPath;

    /**
     * @param string      $id
     * @param string      $url
     * @param string      $httpUsername
     * @param string      $httpPassword
     * @param string|null $thumbnailPath
     */
    public function __construct($id, $url, $httpUsername, $httpPassword, $thumbnailPath = null)
    {
        $this->id = $id;
        $this->url = $url;
        $this->httpUsername = $httpUsername;
        $this->httpPassword = $httpPassword;
        $this->thumbnailPath = $thumbnailPath;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getHttpUsername()
    {
        return $this->httpUsername;
    }

    /**
     * @return string
     */
    public function getHttpPassword()
    {
        return $this->httpPassword;
    }

    /**
     * @return string|null
     */
    public function getThumbnailPath()
    {
        return $this->thumbnailPath;
    }
}
