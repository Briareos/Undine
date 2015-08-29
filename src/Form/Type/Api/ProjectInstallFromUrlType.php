<?php

namespace Undine\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Undine\Api\Command\ProjectInstallFromUrlCommand;
use Undine\Form\Transformer\StringToUriTransformer;

class ProjectInstallFromUrlType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'api__project_install_from_url';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', 'url', [
            'constraints' => [
                new NotBlank(),
                new Url(),
            ],
        ]);
        $builder->get('url')->addViewTransformer(new StringToUriTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectInstallFromUrlCommand::class,
            'empty_data' => function (FormInterface $form) {
                return new ProjectInstallFromUrlCommand($form->get('url')->getData());
            },
        ]);
    }
}
