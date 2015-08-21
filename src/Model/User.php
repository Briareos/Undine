<?php

namespace Undine\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Undine\Security\User\UserActivityAwareInterface;
use Undine\Uid\UidInterface;
use Undine\Uid\UidTrait;

class User implements UserInterface, UidInterface, UserActivityAwareInterface
{
    use UidTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @var string|null
     */
    private $apiToken;

    /**
     * @var ArrayCollection|Site[]
     */
    private $sites;

    /**
     * @var \DateTime|null
     */
    private $lastLoginAt;

    /**
     * @var \DateTime|null
     */
    private $lastActiveAt;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @param string $email
     */
    public function __construct($email)
    {
        $this->email = $email;
        $this->sites = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param string|null $apiToken
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * @return Site[]
     */
    public function getSites()
    {
        return $this->sites->toArray();
    }

    /**
     * @param Site[] $sites
     */
    public function setSites(array $sites)
    {
        $this->sites = new ArrayCollection($sites);
    }

    /**
     * @return \DateTime|null
     */
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }

    /**
     * @param \DateTime|null $lastLoginAt
     *
     * @return $this
     */
    public function setLastLoginAt(\DateTime $lastLoginAt = null)
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastActiveAt()
    {
        return $this->lastActiveAt;
    }

    /**
     * @param \DateTime|null $lastActiveAt
     *
     * @return $this
     */
    public function setLastActiveAt(\DateTime $lastActiveAt = null)
    {
        $this->lastActiveAt = $lastActiveAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
