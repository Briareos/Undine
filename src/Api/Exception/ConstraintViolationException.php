<?php

namespace Undine\Api\Exception;

use Undine\Api\Error\ErrorInterface;

class ConstraintViolationException extends ApiException
{
    /**
     * @var ErrorInterface
     */
    private $error;

    /**
     * @var string|null
     */
    private $path;

    /**
     * @param ErrorInterface $error
     * @param string|null    $path
     */
    public function __construct(ErrorInterface $error, $path = null)
    {
        $this->error = $error;
        $this->path = $path;
        parent::__construct(sprintf('The constraint %s has been violated.', $error->getName()));
    }

    /**
     * @return ErrorInterface
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }
}
