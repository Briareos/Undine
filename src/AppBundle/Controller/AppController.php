<?php

namespace Undine\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Undine\Model\User;

class AppController extends Controller
{
    /**
     * @return User
     */
    public function getUser()
    {
        return parent::getUser();
    }
}
