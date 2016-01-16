<?php

namespace Undine\Api\Error\Site;

use Undine\Api\Error\AbstractError;

class IdNotProvided extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'site.id_not_provided';
    }
}
