<?php

namespace Undine\Event;

use Symfony\Component\EventDispatcher\Event;
use Undine\Model\User;

class UserRegisterEvent extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
