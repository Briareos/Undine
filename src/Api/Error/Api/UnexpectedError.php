<?php

namespace Undine\Api\Error\Api;

use Undine\Api\Error\AbstractError;

/**
 * An exception was thrown somewhere in the Oxygen protocol implementation.
 */
class UnexpectedError extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'api.unexpected_error';
    }
}
