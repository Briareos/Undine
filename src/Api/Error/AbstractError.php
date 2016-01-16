<?php

namespace Undine\Api\Error;

abstract class AbstractError implements ErrorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getName();
    }
}
