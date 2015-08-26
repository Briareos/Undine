<?php

namespace Undine\Oxygen\Action;

interface ActionInterface
{
    /**
     * The action name should map 1:1 to actions in the Oxygen module.
     *
     * @return string
     */
    public function getName();

    /**
     * The list of arguments to pass to the Oxygen module action.
     * The array keys should be argument names in camelCase format. They will automatically get injected into
     * right places in the module (using reflection).
     * Optional arguments can technically be omitted, but don't do it.
     *
     * @return array
     */
    public function getParameters();

    /**
     * The class that should be instantiated to hold the action's response.
     *
     * @see ReactionInterface
     *
     * @return string
     */
    public function getReactionClass();
}
