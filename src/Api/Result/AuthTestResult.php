<?php

namespace Undine\Api\Result;

use Undine\Model\User;

class AuthTestResult extends AbstractResult
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
     * @return array
     */
    public function getData()
    {
        return [
            'user' => $this->user,
        ];
    }
}
