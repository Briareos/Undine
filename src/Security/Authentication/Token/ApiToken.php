<?php

namespace Undine\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Undine\Model\User;

class ApiToken extends AbstractToken
{
    private $apiToken;

    private $providerKey;

    /**
     * @param User|string $user
     * @param string      $apiToken
     * @param string      $providerKey
     * @param array       $roles
     */
    public function __construct($user, $apiToken, $providerKey, array $roles = [])
    {
        parent::__construct($roles);
        $this->apiToken = $apiToken;
        $this->providerKey = $providerKey;
        $this->setUser($user);
        $this->setAuthenticated($user instanceof User && count($roles) > 0);
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        parent::eraseCredentials();
        $this->apiToken = null;
    }

    public function getCredentials()
    {
        return $this->apiToken;
    }

    public function getProviderKey()
    {
        return $this->providerKey;
    }
}
