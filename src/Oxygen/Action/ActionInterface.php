<?php

namespace Undine\Oxygen\Action;

interface ActionInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getParameters();
}
