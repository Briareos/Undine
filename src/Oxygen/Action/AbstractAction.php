<?php

namespace Undine\Oxygen\Action;

abstract class AbstractAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return [];
    }
}
