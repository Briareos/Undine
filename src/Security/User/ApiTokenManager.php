<?php

namespace Undine\Security\User;

use Doctrine\ORM\EntityManager;
use Undine\Model\User;

class ApiTokenManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Generates a random API token for the user and persists it.
     *
     * @param User $user
     */
    public function issueToken(User $user)
    {
        $token = bin2hex(random_bytes(32));
        $user->setApiToken($token);
        $this->em->persist($user);
        $this->em->flush($user);
    }

    /**
     * Remove the random API token from the user.
     *
     * @param User $user
     */
    public function deleteToken(User $user)
    {
        $user->setApiToken(null);
        $this->em->persist($user);
        $this->em->flush($user);
    }

    public function getToken(User $user)
    {
        return $user->getId().'-'.$user->getApiToken();
    }
}
