<?php

namespace Undine\Oxygen\Reaction;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ExtensionUpdateReaction extends AbstractReaction
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('context');
        $resolver->setAllowedTypes('context', 'array');
    }

    /**
     * @return array
     */
    public function getContext()
    {
        return $this->data['context'];
    }
}
