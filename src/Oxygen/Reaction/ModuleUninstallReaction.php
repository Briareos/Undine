<?php

namespace Undine\Oxygen\Reaction;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ModuleUninstallReaction extends AbstractReaction
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('success');
        $resolver->setAllowedTypes('success', 'bool');
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->data['success'];
    }
}
