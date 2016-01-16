<?php

namespace Undine\Api\Error\Site;

use Undine\Api\Error\AbstractError;

class UrlInvalid extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'site.url_invalid';
    }
}
