<?php

namespace Undine\Oxygen\Exception;

use Undine\Api\Error\ConstraintInterface;

class FailedTransaction extends \Exception
{
    /**
     * @var ConstraintInterface
     */
    private $constraint;a
}
