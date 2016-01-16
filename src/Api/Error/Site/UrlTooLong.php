<?php

namespace Undine\Api\Error\Site;

use Undine\Api\Error\AbstractError;

class UrlTooLong extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'site.url_too_long';
    }
}
