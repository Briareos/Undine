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
    public function findByUid($uid)
    {
        return $this->find(User::getIdFromUid($uid));
    }
}
