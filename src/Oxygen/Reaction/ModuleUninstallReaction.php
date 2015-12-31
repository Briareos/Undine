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
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        // @TODO: The data set here will determine if the uninstallation was successful, so we should throw an exception.
        parent::setData($data);
    }
}
