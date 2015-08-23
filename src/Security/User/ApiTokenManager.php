<?php

namespace Undine\Security\User;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Undine\Model\User;

class ApiTokenManager
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var SecureRandom
     */
    private $secureRandom;

    /**
     * @param EntityManager $em
     * @param SecureRandom  $secureRandom
     */
    public function __construct(EntityManager $em, SecureRandom $secureRandom)
    {
        $this->em           = $em;
        $this->secureRandom = $secureRandom;
    }

    /**
     * Generates a random API token for the user and persists it.
     *
     * @param User $user
     */
    public function issueToken(User $user)
    {
        $token = strtoupper(bin2hex($this->secureRandom->nextBytes(32)));
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
        return $user->getUid().'-'.$user->getApiToken();
    }
}
