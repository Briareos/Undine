<?php

namespace Undine\Api\Constraint;

interface ConstraintInterface
{
    /**
     * Constraint name. Should be in format constraintGroup.constraintName.
     *
     * @return string
     */
    public function getName();

    /**
     * Any additional constraint info that should be passed in the API response.
     *
     * @return array
     */
    public function getData();

    /**
     * @return string
     */
    public function __toString();
}
