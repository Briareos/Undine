<?php

namespace Undine\AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormTypeInterface;
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
                return $this->get('doctrine.orm.default_entity_manager');
            case 'userRepository':
                return $this->get('doctrine.repository.user');
            case 'staffRepository':
                return $this->get('doctrine.repository.staff');
            case 'dispatcher':
                return $this->get('event_dispatcher');
            default:
                throw new \BadMethodCallException();
        }
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return parent::getUser();
    }

    /**
     * Creates and returns a named form instance from the type of the form.
     *
     * @param string                   $name
     * @param string|FormTypeInterface $type    The built type of the form
     * @param mixed                    $data    The initial data for the form
     * @param array                    $options Options for the form
     *
     * @return Form
     */
    protected function createNamedForm($name, $type, $data = null, array $options = [])
    {
        return $this->get('form.factory')->createNamedBuilder($name, $type, $data, $options)->getForm();
    }

    /**
     * Creates and returns a named form builder instance.
     *
     * @param string $name
     * @param mixed  $data    The initial data for the form
     * @param array  $options Options for the form
     *
     * @return FormBuilder
     */
    protected function createNamedFormBuilder($name, $data = null, array $options = [])
    {

    }
}
