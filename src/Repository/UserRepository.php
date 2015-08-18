<?php

namespace Undine\Repository;

use Doctrine\ORM\EntityRepository;
use Undine\Model\User;

class UserRepository extends EntityRepository
{
    /**
     * @param string $uid
     *
     * @return User|null
     */
    public function findOneByUid($uid)
    {
        $id = User::getIdFromUid($uid);

        if ($id === null) {
            return null;
        }

        return $this->find($id);
    }
}
