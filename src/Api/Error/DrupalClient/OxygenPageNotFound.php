<?php

namespace Undine\Api\Error\DrupalClient;

use Undine\Api\Error\AbstractError;

class OxygenPageNotFound extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'drupal_client.oxygen_page_not_found';
    }
}
