<?php

namespace Undine\Oxygen\Reaction;

interface ReactionInterface
{
    /**
     * @param array $data The data, as returned by the action.
     */
    public function setData(array $data);
}
