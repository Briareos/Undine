<?php

namespace Undine\Api\Error\Response;

use Undine\Api\Error\AbstractError;

/**
 * Oxygen module's response could not be found in the response body.
 */
class OxygenNotFound extends AbstractError
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'response.oxygen_not_found';
    }
}
