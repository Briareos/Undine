<?php

namespace Undine\Api\Constraint\Site;

use Undine\Api\Constraint\AbstractConstraint;

class UrlInvalidConstraint extends AbstractConstraint
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site.url_invalid';
    }
}
