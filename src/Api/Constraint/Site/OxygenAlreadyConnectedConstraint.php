<?php

namespace Undine\Api\Constraint\Site;

use Undine\Api\Constraint\AbstractConstraint;

class OxygenAlreadyConnectedConstraint extends AbstractConstraint
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
        $this->loginFormFound     = $loginFormFound;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site.already_connected';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'lookedForLoginForm' => $this->lookedForLoginForm,
            'loginFormFound'     => $this->loginFormFound,
        ];
    }
}
