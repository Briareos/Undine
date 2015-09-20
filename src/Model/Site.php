<?php

namespace Undine\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Psr\Http\Message\UriInterface;
use Undine\Model\Site\FtpCredentials;
use Undine\Model\Site\HttpCredentials;
use Undine\Model\Site\SiteState;
use Undine\Uid\UidInterface;
use Undine\Uid\UidTrait;

class Site implements UidInterface
{
    use UidTrait;

    /**
     * @var int
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
     * @var SiteExtension[]
     */
    private $siteExtensions;

    /**
     * @var SiteUpdate[]
     */
    private $siteUpdates;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     */
    private $deletedAt;

    /**
     * @param UriInterface $url
     * @param User         $user
     * @param string       $privateKey
     * @param string       $publicKey
     */
    public function __construct(UriInterface $url, User $user, $privateKey, $publicKey)
    {
        $this->url             = $url;
        $this->user            = $user;
        $this->privateKey      = $privateKey;
        $this->publicKey       = $publicKey;
        $this->siteState       = new SiteState();
        $this->ftpCredentials  = new FtpCredentials();
        $this->httpCredentials = new HttpCredentials();
        $this->siteExtensions  = new ArrayCollection();
        $this->siteUpdates     = new ArrayCollection();
    }

    /**
     * @return int
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
     * @return SiteExtension[]
     */
    public function getSiteExtensions()
    {
        return $this->siteExtensions->toArray();
    }

    /**
     * @param SiteExtension[] $siteExtensions
     *
     * @return $this
     */
    public function setSiteExtensions(array $siteExtensions)
    {
        $this->siteExtensions = new ArrayCollection($siteExtensions);

        return $this;
    }

    /**
     * @return SiteUpdate[]
     */
    public function getSiteUpdates()
    {
        return $this->siteUpdates->toArray();
    }

    /**
     * @param SiteUpdate[] $siteUpdates
     *
     * @return $this
     */
    public function setSiteUpdates(array $siteUpdates)
    {
        $this->siteUpdates = new ArrayCollection($siteUpdates);

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
     * @return \DateTime|null
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}
