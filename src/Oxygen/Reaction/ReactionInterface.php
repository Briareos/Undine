<?php

namespace Undine\Oxygen\Reaction;

use Undine\Oxygen\Reaction\Exception\ReactionException;

interface ReactionInterface
{
    /**
     * @param array $data The data, as returned by the action.
     *
     * @throws ReactionException
     */
    public function setData(array $data);
}
