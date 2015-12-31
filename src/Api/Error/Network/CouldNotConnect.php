<?php

namespace Undine\Api\Error\Network;

use Undine\Api\Error\AbstractError;

class CouldNotConnect extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'network.could_not_connect';
    }
}
