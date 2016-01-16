<?php

namespace Undine\Api\Error\Network;

use Undine\Api\Error\AbstractError;

class CanNotConnect extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'network.can_not_connect';
    }
}
