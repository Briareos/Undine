<?php

namespace Undine\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Undine\Api\Command\ExtensionDownloadFromUrlCommand;

class ExtensionDownloadFromUrlType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', UrlType::class, [
            'constraints' => [
                new NotBlank(),
                new Url(),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExtensionDownloadFromUrlCommand::class,
            'empty_data' => function (FormInterface $form) {
                return new ExtensionDownloadFromUrlCommand($form->get('url')->getData());
            },
        ]);
    }
}
