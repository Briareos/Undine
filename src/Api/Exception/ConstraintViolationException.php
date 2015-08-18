<?php

namespace Undine\Api\Exception;

class ConstraintViolationException extends ApiException
{
    /**
     * @var string
     */
    private $constraintId;

    /**
     * @var string|null
     */
    private $path;

    /**
     * @param string      $constraintId
     * @param string|null $path
     */
    public function __construct($constraintId, $path = null)
    {
        $this->constraintId = $constraintId;
        $this->path         = $path;
        parent::__construct("The constraint $constraintId has been violated.");
    }

    /**
     * @return string
     */
    public function getConstraintId()
    {
        return $this->constraintId;
    }

    /**
     * @return null|string
     */
    public function getPath()
    {
        return $this->path;
    }
}
