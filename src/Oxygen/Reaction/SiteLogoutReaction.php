<?php

namespace Undine\Oxygen\Reaction;

use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteLogoutReaction extends AbstractReaction
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('destroyedSessions');
        $resolver->setAllowedTypes('destroyedSessions', 'int');
    }

    /**
     * @return int
     */
    public function getDestroyedSessions()
    {
        return $this->data['destroyedSessions'];
    }
}
