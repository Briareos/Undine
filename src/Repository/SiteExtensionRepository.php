<?php

namespace Undine\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

class SiteExtensionRepository
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
}
