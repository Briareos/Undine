<?php

namespace Undine\Email;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Undine\Model\User;

class UserResetPasswordEmail extends AbstractTwigEmail
{
    /**
     * @var string
     */
    private $fromEmail;

    /**
     * @var string
     */
    private $fromName;

    /**
     * @var string
     */
    private $brand;

    /**
     * @param string $fromEmail
     * @param string $fromName
     * @param string $brand
     */
    public function __construct($fromEmail, $fromName, $brand)
    {
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->brand = $brand;
    }

    /**
     * @param array $parameters
     *
     * @return \Swift_Message
     */
    public function createMessage(array $parameters = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['user', 'resetUrl']);
        $resolver->setAllowedTypes('user', User::class);
        $resolver->setAllowedTypes('resetUrl', 'string');
        $parameters = $resolver->resolve($parameters);

        /** @var User $user */
        $user = $parameters['user'];

        $message = \Swift_Message::newInstance();
        $message->setSubject(sprintf('[%s] Password reset request', $this->brand));
        $message->setTo($user->getEmail(), $user->getName());
        $message->setFrom($this->fromEmail, $this->fromName);
        $message->setBody($this->twig->render('email/user/reset-password.html.twig', $parameters), 'text/html');

        return $message;
    }
}
