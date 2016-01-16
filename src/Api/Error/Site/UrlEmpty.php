<?php

namespace Undine\Api\Error\Site;

use Undine\Api\Error\AbstractError;

class UrlEmpty extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'site.empty_url';
    }
}
