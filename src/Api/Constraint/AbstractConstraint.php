<?php

namespace Undine\Api\Constraint;

abstract class AbstractConstraint implements ConstraintInterface
{
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
