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

    /**
     * @return string
     */
    public function getReactionClass()
    {
        // Convert Root\Namespace\Action\TestAction to Root\Namespace\Reaction\TestReaction.
        // Those slashes seem weird, but they are necessary, really. Compilation fails without them.
        return preg_replace('{(?:(\\\\)Action(\\\\)|Action$)}', '$1Reaction$2', get_class($this));
    }
}
