<?php

namespace Undine\AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Undine\Model\User;
use Undine\Repository\StaffRepository;
use Undine\Repository\UserRepository;

/**
 * @property EntityManager   $em
 * @property UserRepository  $userRepository
 * @property StaffRepository $staffRepository
 */
abstract class AppController extends Controller
{
    function __get($name)
    {
        switch ($name) {
            case 'em':
                return $this->container->get('doctrine.orm.default_entity_manager');
            case 'userRepository':
                return $this->container->get('doctrine.repository.user');
            case 'staffRepository':
                return $this->container->get('doctrine.repository.staff');
            default:
                throw new \BadMethodCallException();
        }
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return parent::getUser();
    }
}
