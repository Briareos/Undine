<?php

namespace Undine\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Psr\Http\Message\UriInterface;
use Undine\Model\Site\FtpCredentials;
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
     * @var SiteExtension[]
     */
    private $siteExtensions;

    /**
     * @var SiteUpdate[]
     */
    private $siteUpdates;

    /**
     * @var string|null
     */
    private $httpUsername;

    /**
     * @var string|null
     */
    private $httpPassword;

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
        $this->url            = $url;
        $this->user           = $user;
        $this->privateKey     = $privateKey;
        $this->publicKey      = $publicKey;
        $this->siteState      = new SiteState();
        $this->ftpCredentials = new FtpCredentials();
        $this->siteExtensions = new ArrayCollection();
        $this->siteUpdates    = new ArrayCollection();
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
     * @return FtpCredentials
     */
    public function getFtpCredentials()
    {
        return $this->ftpCredentials;
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
     * @return string|null
     */
    public function getHttpUsername()
    {
        return $this->httpUsername;
    }

    /**
     * @param string|null $httpUsername
     *
     * @return $this
     */
    public function setHttpUsername($httpUsername = null)
    {
        $this->httpUsername = $httpUsername;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHttpPassword()
    {
        return $this->httpPassword;
    }

    /**
     * @param string|null $httpPassword
     *
     * @return $this
     */
    public function setHttpPassword($httpPassword = null)
    {
        $this->httpPassword = $httpPassword;

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
