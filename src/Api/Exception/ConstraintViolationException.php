<?php

namespace Undine\Api\Exception;

use Undine\Api\Constraint\ConstraintInterface;

class ConstraintViolationException extends ApiException
{
    /**
     * @var ConstraintInterface
     */
    private $constraint;

    /**
     * @var string|null
     */
    private $path;

    /**
     * @param ConstraintInterface $constraint
     * @param string|null         $path
     */
    public function __construct(ConstraintInterface $constraint, $path = null)
    {
        $this->constraint = $constraint;
        $this->path       = $path;
        parent::__construct("The constraint {$constraint->getName()} has been violated.");
    }

    /**
     * @return ConstraintInterface
     */
    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }
}
