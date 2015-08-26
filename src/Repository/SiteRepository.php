<?php

namespace Undine\Repository;

use Doctrine\ORM\EntityRepository;
use Undine\Model\Site;

class SiteRepository extends EntityRepository
{
    /**
     * @param string $uid
     *
     * @return Site|null
     */
    public function findOneByUid($uid)
    {
        $id = Site::getIdFromUid($uid);

        if ($id === null) {
            return null;
        }

        return $this->find($id);
    }
}
