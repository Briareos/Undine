<?php

namespace Undine\Api\Constraint\Site;

use Undine\Api\Constraint\AbstractConstraint;

class HttpAuthenticationFailedConstraint extends AbstractConstraint
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site.http_authentication_failed';
    }

}
