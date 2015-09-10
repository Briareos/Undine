<?php

namespace Undine\Oxygen\Reaction;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ExtensionDownloadFromUrlReaction extends AbstractReaction
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
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        // @TODO: The data set here will determine if the installation was successful, so we should throw an exception.
        parent::setData($data);
    }
}
