<?php

namespace Undine\Api\Constraint\Security;

use Undine\Api\Constraint\AbstractConstraint;

class BadCredentialsConstraint extends AbstractConstraint
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'security.bad_credentials';
    }
}
