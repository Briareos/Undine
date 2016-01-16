<?php

namespace Undine\Email;

class EmailFactory
{
    /**
     * @var EmailInterface[]
     */
    private $registry;

    public function registerEmail($type, EmailInterface $email)
    {
        $this->registry[$type] = $email;
    }

    /**
     * @see AppEmails
     *
     * @param string $type       One of AppEmails constants.
     * @param array  $parameters Parameters to be used in the email.
     *
     * @return \Swift_Message
     */
    public function createEmail($type, array $parameters = [])
    {
        if (!isset($this->registry[$type])) {
            throw new \InvalidArgumentException(sprintf('The email type "%s" is not registered.', $type));
        }

        return $this->registry[$type]->createMessage($parameters);
    }
}
