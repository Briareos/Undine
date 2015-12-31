<?php

namespace Undine\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Undine\Api\Command\ModuleDisableCommand;

class ModuleDisableType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('module', TextType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('disableDependents', CheckboxType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModuleDisableCommand::class,
            'empty_data' => function (FormInterface $form) {
                return new ModuleDisableCommand($form->get('module')->getData(), $form->get('disableDependents')->getData());
            },
        ]);
    }
}
