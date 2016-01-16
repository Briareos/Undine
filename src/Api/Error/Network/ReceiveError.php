<?php

namespace Undine\Api\Error\Network;

use Undine\Api\Error\AbstractError;

class ReceiveError extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'network.receive_error';
    }
}
