<?php

namespace Undine\Api\Error\Security;

use Undine\Api\Error\AbstractError;

class NotAuthenticated extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'security.not_authenticated';
    }
}
