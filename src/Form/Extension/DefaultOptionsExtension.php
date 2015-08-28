<?php

namespace Undine\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Override Symfony's default form options (eg. 'required' => true).
 */
class DefaultOptionsExtension extends AbstractTypeExtension
{
    private $defaultOptions;

    public function __construct(array $defaultOptions)
    {
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults($this->defaultOptions);
    }
}
