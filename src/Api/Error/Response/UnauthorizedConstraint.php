<?php

namespace Undine\Api\Error\Response;

use Undine\Api\Error\AbstractError;

/**
 * We got an HTTP authorization request.
 */
class UnauthorizedConstraint extends AbstractError
{
    /**
     * @var string
     */
    private $realm;

    /**
     * @var bool
     */
    private $hasCredentials;

    /**
     * @param string $realm
     * @param bool   $hasCredentials
     */
    public function __construct($realm, $hasCredentials)
    {
        $this->realm          = $realm;
        $this->hasCredentials = $hasCredentials;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'response.unauthorized';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'realm'          => $this->realm,
            'hasCredentials' => $this->hasCredentials,
        ];
    }
}
