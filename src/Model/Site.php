<?php

namespace Undine\Model;

use Psr\Http\Message\UriInterface;
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
     * Drupal's internal "site_key", used for statistic tracking.
     *
     * @var string|null
     */
    private $siteKey;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @param UriInterface $url
     * @param User         $user
     * @param string       $privateKey
     * @param string       $publicKey
     */
    public function __construct(UriInterface $url, User $user, $privateKey, $publicKey)
    {
        $this->url        = $url;
        $this->user       = $user;
        $this->privateKey = $privateKey;
        $this->publicKey  = $publicKey;
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
     * @return string|null
     */
    public function getSiteKey()
    {
        return $this->siteKey;
    }

    /**
     * @param string|null $siteKey
     *
     * @return $this
     */
    public function setSiteKey($siteKey)
    {
        $this->siteKey = $siteKey;

        return $this;
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
