<?php

namespace Undine\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Undine\Security\User\UserActivityAwareInterface;

class User implements UserInterface, UserActivityAwareInterface, EquatableInterface, \Serializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $plainPassword;

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
     * @param string $name
     * @param string $email
     */
    public function __construct($name, $email)
    {
        $this->id = \Undine\Functions\generate_uuid();
        $this->name = $name;
        $this->email = $email;
        $this->sites = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
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
     * @param string|null $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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

    public function hasApiToken()
    {
        return (bool)strlen($this->apiToken);
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
    public function setApiToken($apiToken = null)
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
     *
     * @return $this
     */
    public function setSites(array $sites)
    {
        $this->sites = new ArrayCollection($sites);

        return $this;
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
     * @return string|null
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     *
     * @return $this
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

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
     * {@inheritdoc}
     */
    public function isEqualTo(UserInterface $user)
    {
        if ($this->getPassword() !== $user->getPassword()) {
            return false;
        }

        $currentRoles = array_map('strval', $this->getRoles());
        $passedRoles = array_map('strval', $user->getRoles());
        sort($currentRoles);
        sort($passedRoles);

        if ($currentRoles !== $passedRoles) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->id);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->id = unserialize($serialized);
    }
}
