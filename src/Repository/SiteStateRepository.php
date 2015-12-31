<?php

namespace Undine\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Undine\Model\Site;
use Undine\Model\SiteState;

class SiteStateRepository
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
     * @param string $id Site state UUID.
     *
     * @return SiteState|null
     */
    public function find($id)
    {
        if (!\Undine\Functions\valid_uuid1($id)) {
            return null;
        }

        return $this->em->find(SiteState::class, (string)$id);
    }
}
