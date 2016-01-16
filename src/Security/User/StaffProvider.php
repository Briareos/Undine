<?php

namespace Undine\Security\User;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Undine\Model\Staff;
use Undine\Repository\StaffRepository;

class StaffProvider implements UserProviderInterface
{
    /**
     * @var StaffRepository
     */
    private $staffRepository;

    /**
     * @param StaffRepository $staffRepository
     */
    public function __construct(StaffRepository $staffRepository)
    {
        $this->staffRepository = $staffRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->staffRepository->findOneByEmail($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof Staff) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $refreshedUser = $this->staffRepository->find($user->getId());

        if (null === $refreshedUser) {
            throw new UsernameNotFoundException(sprintf('User with ID "%s" not found', $user->getId()));
        }

        return $refreshedUser;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === Staff::class || is_subclass_of($class, Staff::class);
    }
}
