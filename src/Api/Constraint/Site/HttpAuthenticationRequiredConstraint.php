<?php

namespace Undine\Api\Constraint\Site;

use Undine\Api\Constraint\AbstractConstraint;

class HttpAuthenticationRequiredConstraint extends AbstractConstraint
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site.http_authentication_required';
    }
}
