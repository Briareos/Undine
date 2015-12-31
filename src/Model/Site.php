<?php

namespace Undine\Model;

use Psr\Http\Message\UriInterface;
use Ramsey\Uuid\Uuid;
use Undine\Model\Site\FtpCredentials;
use Undine\Model\Site\HttpCredentials;

class Site
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var UriInterface
     */
    private $url;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var SiteState
     */
    private $siteState;

    /**
     * @var FtpCredentials
     */
    private $ftpCredentials;

    /**
     * @var HttpCredentials
     */
    private $httpCredentials;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var string|null
     */
    private $thumbnailUrl;

    /**
     * @var \DateTime|null
     */
    private $thumbnailUpdatedAt;



    /**
     * @param UriInterface $url
     * @param User         $user
     * @param string       $privateKey
     * @param string       $publicKey
     */
    public function __construct(UriInterface $url, User $user, $privateKey, $publicKey)
    {
        $this->id              = \Undine\Functions\generate_uuid1();
        $this->url             = $url;
        $this->user            = $user;
        $this->privateKey      = $privateKey;
        $this->publicKey       = $publicKey;
        $this->siteState       = new SiteState($this);
        $this->ftpCredentials  = new FtpCredentials();
        $this->httpCredentials = new HttpCredentials();
        $this->createdAt       = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UriInterface
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param UriInterface $url
     *
     * @return $this
     */
    public function setUrl(UriInterface $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @return SiteState
     */
    public function getSiteState()
    {
        return $this->siteState;
    }

    /**
     * @return bool
     */
    public function hasFtpCredentials()
    {
        return $this->ftpCredentials->present();
    }

    /**
     * @return FtpCredentials
     */
    public function getFtpCredentials()
    {
        return $this->ftpCredentials;
    }

    /**
     * @param FtpCredentials|null $credentials
     *
     * @return $this
     */
    public function setFtpCredentials(FtpCredentials $credentials = null)
    {
        if ($credentials === null) {
            $this->ftpCredentials->clear();
        } else {
            $this->ftpCredentials->fillWith($credentials);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasHttpCredentials()
    {
        return $this->httpCredentials->present();
    }

    /**
     * @return HttpCredentials
     */
    public function getHttpCredentials()
    {
        return $this->httpCredentials;
    }

    /**
     * @param HttpCredentials|null $credentials
     *
     * @return $this
     */
    public function setHttpCredentials(HttpCredentials $credentials = null)
    {
        if ($credentials === null) {
            $this->httpCredentials->clear();
        } else {
            $this->httpCredentials->fillWith($credentials);
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getThumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

    /**
     * @param string|null $thumbnailUrl
     */
    public function setThumbnailUrl($thumbnailUrl = null)
    {
        $this->thumbnailUrl       = $thumbnailUrl;
        $this->thumbnailUpdatedAt = new \DateTime();
    }

    /**
     * @return \DateTime|null
     */
    public function getThumbnailUpdatedAt()
    {
        return $this->thumbnailUpdatedAt;
    }
}
