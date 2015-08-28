<?php

namespace Undine\Oxygen\Reaction;

use Symfony\Component\OptionsResolver\OptionsResolver;

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

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
