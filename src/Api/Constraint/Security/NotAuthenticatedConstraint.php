<?php

namespace Undine\Api\Constraint\Security;

use Undine\Api\Constraint\AbstractConstraint;

class NotAuthenticatedConstraint extends AbstractConstraint
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'security.not_authenticated';
    }
}
