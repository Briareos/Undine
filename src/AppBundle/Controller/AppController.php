<?php

namespace Undine\AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Undine\Model\User;
use Undine\Repository\StaffRepository;
use Undine\Repository\UserRepository;

/**
 * @property EntityManager            $em
 * @property UserRepository           $userRepository
 * @property StaffRepository          $staffRepository
 * @property EventDispatcherInterface $dispatcher
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
            case 'dispatcher':
                return $this->container->get('event_dispatcher');
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
