<?php

namespace Undine\Api\Error\DrupalClient;

use Undine\Api\Error\AbstractError;

class InvalidCredentials extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'drupal_client.invalid_credentials';
    }
}
