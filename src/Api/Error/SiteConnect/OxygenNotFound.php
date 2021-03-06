<?php

namespace Undine\Api\Error\SiteConnect;

use Undine\Api\Error\AbstractError;

class OxygenNotFound extends AbstractError
{
    /**
     * @var bool
     */
    private $lookedForLoginForm;

    /**
     * @var bool
     */
    private $loginFormFound;

    /**
     * @param bool $lookedForLoginForm
     * @param bool $loginFormFound
     */
    public function __construct($lookedForLoginForm, $loginFormFound)
    {
        $this->lookedForLoginForm = $lookedForLoginForm;
        $this->loginFormFound = $loginFormFound;
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'site_connect.oxygen_not_found';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'lookedForLoginForm' => $this->lookedForLoginForm,
            'loginFormFound' => $this->loginFormFound,
        ];
    }
}
