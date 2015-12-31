<?php

namespace Undine\Api\Error\Security;

use Undine\Api\Error\AbstractError;

class BadCredentials extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'security.bad_credentials';
    }
}
