<?php

namespace Undine\AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Undine\AppBundle\Controller\AppController;
use Undine\Email\Emails;
use Undine\Event\Events;
use Undine\Event\UserRegisterEvent;
use Undine\Event\UserResetPasswordEvent;
use Undine\Model\User;
use Undine\Web\Command\RegistrationCommand;

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

    /**
     * @Route("/register", name="web-register")
     * @Template("web/security/register.html.twig")
     */
    public function registerAction(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // The user is already authenticated.
            return $this->redirectToRoute('web-home');
        }

        $form = $this->createForm('web__registration', null, [
            'method' => 'POST',
            'action' => $this->generateUrl('web-register'),
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var RegistrationCommand $command */
            $command  = $form->getData();
            $password = $this->get('security.encoder_factory')->getEncoder(User::class)->encodePassword($command->getPlainPassword(), '');
            $user     = (new User($command->getName(), $command->getEmail()))
                ->setPassword($password);
            $this->em->persist($user);
            $this->em->flush($user);

            // Log the user In. Check how UserAuthenticationProvider does it.
            $token = new UsernamePasswordToken($user, $command->getPlainPassword(), 'web', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);

            // Dispatch the app event.
            $registerEvent = new UserRegisterEvent($user);
            $this->dispatcher->dispatch(Events::USER_REGISTER, $registerEvent);

            // Since we're logging the user in manually, we should also dispatch the appropriate event.
            $loginEvent = new InteractiveLoginEvent($request, $token);
            $this->dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);

            return $this->redirectToRoute('web-dashboard');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/reset-password", name="web-reset_password")
     * @Template("web/security/reset-password.html.twig")
     */
    public function resetPasswordAction(Request $request)
    {
        $formOptions     = [
            'method' => 'POST',
            'action' => $this->generateUrl('web-reset_password'),
        ];
        $isAuthenticated = $this->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if ($isAuthenticated) {
            // The user is authenticated.
            $form = $this->createNamedForm('request_password', 'form', $formOptions);
            $user = $this->getUser();
        } else {
            // This might look weird, but the event listener below will set this value if the form's valid.
            // If the form is not valid, the $user variable will never be used.
            $user = null;
            $form = $this->createNamedFormBuilder('reset_password', null, $formOptions)
                ->add('email', 'email', [
                    'constraints' => [
                        new Type(['type' => 'string']),
                        new NotBlank(),
                        new Email(),
                    ],
                ])
                ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use (&$user) {
                    $data = $event->getData();
                    if (empty($data['email']) || !is_string($data['email'])) {
                        // Other validators will pick up from here.
                        return;
                    }
                    if ($user = $this->userRepository->findOneBy(['email' => $data['email']])) {
                        return;
                    }
                    $event->getForm()->get('email')->addError(new FormError('A user with the specified email address could not be found.'));
                })
                ->getForm();
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $resetUrl   = $this->generateResetUrl($user, $this->currentTime->getTimestamp());
            $resetEmail = $this->emailFactory->createEmail(Emails::USER_RESET_PASSWORD, [
                'user'     => $user,
                'resetUrl' => $resetUrl,
            ]);
            $this->mailer->send($resetEmail);

            $this->addFlash('success', 'Further instructions have been sent to your e-mail address.');

            return $this->redirectToRoute('web-login');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param User $user
     * @param int  $currentTimestamp
     *
     * @return string
     */
    private function generateResetUrl(User $user, $currentTimestamp)
    {
        $hash = $this->getLoginToken($user, $currentTimestamp);

        $url = $this->generateUrl('web-set_password', [
            'uid'       => $user->getUid(),
            'timestamp' => $currentTimestamp,
            'hash'      => $hash,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $url;
    }

    private function getLoginToken(User $user, $timestamp)
    {
        $hash = base64_encode(
            hash_hmac(
                'sha256',
                sprintf('%d|%s',
                    $timestamp,
                    $user->getUid()
                ),
                sprintf('%s|%s',
                    $user->getPassword(),
                    $this->getParameter('secret')
                ),
                true
            )
        );

        // URL-friendly hash.
        return strtr($hash, ['+' => '-', '/' => '_', '=' => '']);
    }

    /**
     * @Route("/set-password/{uid}/{timestamp}/{hash}", name="web-set_password", requirements={"uid"="^U\d{10}$", "timestamp"="^\d+$", "hash"="^[a-zA-Z0-9_-]+$"})
     * @ParamConverter("resetUser", class="Model:User", options={"id":"uid", "repository_method":"findOneByUid"})
     * @Template("web/security/set-password.html.twig")
     */
    public function setPassword(User $resetUser, $timestamp, $hash, Request $request)
    {
        $currentUser = $this->getUser();

        if ($currentUser && $currentUser->getUid() !== $resetUser->getUid()) {
            // User is logged in as a different user.
            $this->addFlash('error', sprintf('Another user (<em>%s</em>) is already logged into the site on this computer, but you tried to use a one-time link for user <em>%s</em>. Please <a href="%s">logout</a> and try using the link again.', $currentUser->getName(), $resetUser->getName(), $this->container->get('security.logout_url_generator')->getLogoutUrl('web')));

            return $this->redirectToRoute('web-home');
        }

        $timeout = 86400;

        // No time out for first time login.
        if ($resetUser->getLastLoginAt() && ($timestamp + $timeout <= $this->currentTime->getTimestamp())) {
            $this->addFlash('error', 'You have tried to use a one-time login link that has expired. Please request a new one using the form below.');

            return $this->redirectToRoute('web-reset_password');
        }

        if ($resetUser->getLastLoginAt() && $resetUser->getLastLoginAt()->getTimestamp() >= $timestamp) {
            // The user was logged in after this login URL was generated. Show different message?
            $this->addFlash('error', 'You have tried to use a one-time login link that has either been used or is no longer valid. Please request a new one using the form below');

            return $this->redirectToRoute('web-reset_password');
        }

        if (!hash_equals($this->getLoginToken($resetUser, $timestamp), $hash)) {
            // The hash is plain wrong. This is a sign of attack.
            $this->logger->notice('Invalid reset password token used.', [
                'ip'        => $request->getClientIp(),
                'userId'    => $resetUser->getId(),
                'userName'  => $resetUser->getName(),
                'userEmail' => $resetUser->getEmail(),
            ]);
            $this->addFlash('error', 'You have tried to use a one-time login link that has either been used or is no longer valid. Please request a new one using the form below.');

            return $this->redirectToRoute('web-reset_password');
        }

        $form = $this->createFormBuilder(null, [
            'method' => 'POST',
            // Current URL.
            'action' => $this->generateUrl('web-set_password', ['uid' => $resetUser->getUid(), 'timestamp' => $timestamp, 'hash' => $hash]),
        ])
            ->add('password', 'password', [
                'constraints' => [
                    new Type(['type' => 'string']),
                    new NotBlank(),
                ],
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $plainPassword = $form->getData()['password'];
            $password      = $this->get('security.encoder_factory')->getEncoder(User::class)->encodePassword($plainPassword, '');
            $resetUser->setPassword($password);
            $this->em->persist($resetUser);
            $this->em->flush($resetUser);

            $this->addFlash('success', 'You have successfully reset your password and have been logged in.');

            // Log the user In. Check how UserAuthenticationProvider does it.
            $token = new UsernamePasswordToken($resetUser, $plainPassword, 'web', $resetUser->getRoles());
            $this->get('security.token_storage')->setToken($token);

            // Dispatch the app event.
            $resetPasswordEvent = new UserResetPasswordEvent($resetUser);
            $this->dispatcher->dispatch(Events::USER_RESET_PASSWORD, $resetPasswordEvent);

            // Since we're logging the user in manually, we should also dispatch the appropriate event.
            $loginEvent = new InteractiveLoginEvent($request, $token);
            $this->dispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);

            return $this->redirectToRoute('web-home');
        }

        return [
            'form' => $form->createView(),
        ];
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
