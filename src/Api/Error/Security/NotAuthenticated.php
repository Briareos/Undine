<?php

namespace Undine\Api\Error\Security;

use Undine\Api\Error\AbstractError;

class NotAuthenticated extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'security.not_authenticated';
    }
}
