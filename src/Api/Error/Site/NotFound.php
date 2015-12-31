<?php

namespace Undine\Api\Error\Site;

use Undine\Api\Error\AbstractError;

class NotFound extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site.not_found';
    }
}
