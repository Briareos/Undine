<?php

namespace Undine\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Undine\Model\User;

class UserRepository
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ClassMetadata
     */
    private $metadata;

    public function __construct(EntityManager $em, ClassMetadata $metadata)
    {
        $this->em = $em;
        $this->metadata = $metadata;
    }

    /**
     * @param string $id User UUID.
     *
     * @return User|null
     */
    public function find($id)
    {
        if (!\Undine\Functions\valid_uuid($id)) {
            return null;
        }

        return $this->em->find(User::class, (string)$id);
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function findOneByEmail($email)
    {
        $persister = $this->em->getUnitOfWork()->getEntityPersister(User::class);

        return $persister->load(['email' => (string)$email], null, null, [], null, 1);
    }

    /**
     * @return User[]
     */
    public function findAll()
    {
        return $this->em->getUnitOfWork()->getEntityPersister(User::class)->loadAll();
    }
}
