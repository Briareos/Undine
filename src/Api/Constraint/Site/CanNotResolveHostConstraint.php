<?php

namespace Undine\Api\Constraint\Site;

use Undine\Api\Constraint\AbstractConstraint;

class CanNotResolveHostConstraint extends AbstractConstraint
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site.can_not_resolve_host';
    }
}
