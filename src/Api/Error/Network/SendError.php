<?php

namespace Undine\Api\Error\Network;

use Undine\Api\Error\AbstractError;

class SendError extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'network.send_error';
    }
}
