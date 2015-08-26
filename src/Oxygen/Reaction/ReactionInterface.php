<?php

namespace Undine\Oxygen\Reaction;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ReactionInterface
{
    /**
     * @param array $data The data, as returned by the action.
     */
    public function setData(array $data);

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);
}
