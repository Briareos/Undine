<?php

namespace Undine\Api\Error\Ftp;

use Undine\Api\Error\AbstractError;

class CredentialsRequired extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'ftp.credentials_required';
    }
}
