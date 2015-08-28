<?php

namespace Undine\AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Undine\AppBundle\Controller\AppController;

class SecurityController extends AppController
{
    /**
     * @Route("/login", name="web-login")
     * @Template("web/security/login.html.twig")
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        } else {
            $error = null;
        }
        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(Security::LAST_USERNAME);
        $csrfToken    = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();

        return [
            'lastUsername' => $lastUsername,
            'error'        => $error,
            'csrfToken'    => $csrfToken,
        ];
    }

    public function registerAction()
    {

    }

    public function resetPasswordAction()
    {

    }

    public function deactivateAccountAction()
    {

    }

    /**
     * @Route("/login_check", name="web-login_check")
     */
    public function loginCheckAction()
    {
        throw new \LogicException('This method should be intercepted by the firewall.');
    }

    /**
     * @Route("/logout", name="web-logout")
     */
    public function logoutAction()
    {
        throw new \LogicException('This method should be intercepted by the firewall.');
    }
}
