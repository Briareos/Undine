<?php

namespace Undine\Model;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Undine\Security\User\UserActivityAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Staff implements UserInterface, UserActivityAwareInterface, EquatableInterface, \Serializable
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
     * @var string
     */
    private $password;

    /**
     * @var string|null
     */
    private $plainPassword;

    /**
     * @var \DateTime|null
     */
    private $lastActiveAt;

    /**
     * @var \DateTime|null
     */
    private $lastLoginAt;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @param string $email
     * @param string $password
     */
    public function __construct($name, $email, $password)
    {
        $this->id = \Undine\Functions\generate_uuid();
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function getRoles()
    {
        return ['ROLE_ADMIN'];
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
     */
    public function setName($name)
    {
        $this->name = $name;
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

    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

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
    public function setPlainPassword($plainPassword = null)
    {
        if (is_string($plainPassword) && strlen($plainPassword) === null) {
            $plainPassword = null;
        }

        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
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
     * @return string
     */
    public function getAvatarUrl()
    {
        return '//www.gravatar.com/avatar/'.md5($this->email);
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
