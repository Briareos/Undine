<?php

namespace Undine\Oxygen\Reaction;

abstract class AbstractReaction implements ReactionInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
