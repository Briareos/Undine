<?php

namespace Undine\Security\User;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Undine\Model\User;
use Undine\Repository\UserRepository;

class ApiTokenUserProvider implements UserProviderInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Loads the user for the given token.
     *
     * @param string $token The token.
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException If the user is not found.
     */
    public function loadUserByApiToken($token)
    {
        if (!preg_match('{^\w+-\w+$}', $token)) {
            throw new UsernameNotFoundException('The token is invalid.');
        }

        list($userUid, $token) = explode('-', $token, 2);

        $user = $this->userRepository->findOneByUid($userUid);

        if ($user === null) {
            throw new UsernameNotFoundException('User not found.');
        }

        if (!$user->getApiToken()) {
            throw new UsernameNotFoundException('The user does not have token-based API access enabled.');
        }

        if (!hash_equals($user->getApiToken(), $token)) {
            throw new UsernameNotFoundException('User not found.');
        }

        return $user;
    }

    /**
     * This method is not supported by this provider.
     *
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        throw new UsernameNotFoundException(sprintf('The method %s is not supported by the %s.', __METHOD__, __CLASS__));
    }

    /**
     * This method is not supported by this provider.
     *
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === User::class;
    }
}
