<?php

namespace Undine\Web\Command;

class RegistrationCommand
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * @param string $name
     * @param string $email
     * @param string $plainPassword
     */
    public function __construct($name, $email, $plainPassword)
    {
        $this->name          = $name;
        $this->email         = $email;
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
}
