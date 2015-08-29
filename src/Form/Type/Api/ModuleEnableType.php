<?php

namespace Undine\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Undine\Api\Command\ModuleEnableCommand;

class ModuleEnableType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'api__module_enable';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('module', 'collection', [
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('enableDependencies', 'checkbox');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModuleEnableCommand::class,
            'empty_data' => function (FormInterface $form) {
                return new ModuleEnableCommand($form->get('modules')->getData(), $form->get('enableDependencies')->getData());
            },
        ]);
    }
}
