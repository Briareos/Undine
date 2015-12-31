<?php

namespace Undine\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Undine\Model\Staff;

class StaffRepository
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
        $this->em       = $em;
        $this->metadata = $metadata;
    }

    /**
     * @param string $id Staff member UUID.
     *
     * @return Staff|null
     */
    public function find($id)
    {
        if (!\Undine\Functions\valid_uuid1($id)) {
            return null;
        }

        return $this->em->find(Staff::class, (string)$id);
    }

    /**
     * @param string $email
     *
     * @return Staff|null
     */
    public function findOneByEmail($email)
    {
        $persister = $this->em->getUnitOfWork()->getEntityPersister(Staff::class);

        return $persister->load(['email' => (string)$email], null, null, [], null, 1);
    }

    /**
     * @return Staff[]
     */
    public function findAll()
    {
        return $this->em->getUnitOfWork()->getEntityPersister(Staff::class)->loadAll();
    }
}
