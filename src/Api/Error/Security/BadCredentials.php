<?php

namespace Undine\Api\Error\Security;

use Undine\Api\Error\AbstractError;

class BadCredentials extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'security.bad_credentials';
    }
}
