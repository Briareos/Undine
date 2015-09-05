<?php

namespace Undine\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Psr\Http\Message\UriInterface;
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
     * @var SiteExtension[]
     */
    private $siteExtensions;

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
        $this->siteExtensions = new ArrayCollection();
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
     * @return SiteExtension[]
     */
    public function getSiteExtensions()
    {
        return $this->siteExtensions->toArray();
    }

    /**
     * @param SiteExtension[] $siteExtensions
     */
    public function setSiteExtensions(array $siteExtensions)
    {
        $this->siteExtensions = new ArrayCollection($siteExtensions);
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
