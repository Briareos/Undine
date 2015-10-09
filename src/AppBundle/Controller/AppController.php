<?php

namespace Undine\AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Undine\Api\Progress\ProgressInterface;
use Undine\Email\EmailFactory;
use Undine\Model\User;
use Undine\Oxygen\Client as OxygenClient;
use Undine\Oxygen\LoginUrlGenerator;
use Undine\Repository\StaffRepository;
use Undine\Repository\UserRepository;
use Undine\Security\Authentication\Token\ApiToken;

/**
 * @property EntityManager            $em
 * @property UserRepository           $userRepository
 * @property StaffRepository          $staffRepository
 * @property EventDispatcherInterface $dispatcher
 * @property OxygenClient             $oxygenClient
 * @property LoginUrlGenerator        $oxygenLoginUrlGenerator
 * @property \DateTime                $currentTime
 * @property EmailFactory             $emailFactory
 * @property \Swift_Mailer            $mailer
 * @property Logger                   $logger
 * @property Session                  $session
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
            case 'oxygenClient':
                return $this->get('undine.oxygen.client');
            case 'oxygenLoginUrlGenerator';
                return $this->get('undine.oxygen.login_url_generator');
            case 'currentTime':
                return $this->get('current_time');
            case 'emailFactory':
                return $this->get('undine.email.factory');
            case 'mailer':
                return $this->get('swiftmailer.mailer.default');
            case 'logger':
                return $this->get('logger');
            case 'session':
                return $this->get('session');
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
        return $this->get('form.factory')->createNamedBuilder($name, 'form', $data, $options);
    }

    /**
     * @return bool Whether the current call is stateless (should not use sessions).
     */
    protected function isStateless()
    {
        return ($this->get('security.token_storage')->getToken() instanceof ApiToken);
    }

    protected function progress(ProgressInterface $progress)
    {
        $this->get('output_flusher')->flushMessage(json_encode($progress));
    }
}
