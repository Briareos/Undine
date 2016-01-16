<?php

namespace Undine\Event;

use Symfony\Component\HttpFoundation\Request;
use Undine\Model\User;

class UserResetPasswordFailedEvent
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var User
     */
    private $user;

    /**
     * @param Request $request
     * @param User    $user
     */
    public function __construct(Request $request, User $user)
    {
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
