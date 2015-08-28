<?php

namespace Undine\Oxygen\Reaction;

use Symfony\Component\OptionsResolver\OptionsResolver;

class SitePingReaction extends AbstractReaction
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('pong');
        $resolver->setRequired('pong');
        $resolver->setAllowedTypes('pong', 'string');
    }

    /**
     * @return string
     */
    public function getPong()
    {
        return $this->data['pong'];
    }
}
