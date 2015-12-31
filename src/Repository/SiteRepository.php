<?php

namespace Undine\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Undine\Model\Site;

class SiteRepository
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
     * @param string $id Site UUID.
     *
     * @return Site|null
     */
    public function find($id)
    {
        if (!\Undine\Functions\valid_uuid1($id)) {
            return null;
        }

        return $this->em->find(Site::class, (string)$id);
    }

    /**
     * @param string $alias
     * @param string $indexBy The index for the from.
     *
     * @return QueryBuilder
     *
     * @internal Used by form+doctrine bridge, don't ever use this directly.
     */
    public function createQueryBuilder($alias, $indexBy = null)
    {
        return $this->em->createQueryBuilder()
            ->select($alias)
            ->from(Site::class, $alias, $indexBy);
    }
}
