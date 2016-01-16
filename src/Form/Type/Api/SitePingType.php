<?php

namespace Undine\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Undine\Api\Command\SitePingCommand;
use Undine\Api\Error\Site\NotFound;
use Undine\Api\Error\Site\IdNotProvided;
use Undine\Form\Type\ModelType;
use Undine\Model\Site;

class SitePingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('site', ModelType::class, [
            'model' => Site::class,
            'invalid_message' => new NotFound(),
            'constraints' => [
                new NotBlank([
                    'message' => new IdNotProvided(),
                ]),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SitePingCommand::class,
            'empty_data' => function (FormInterface $form) {
                return new SitePingCommand($form->get('site')->getData());
            },
        ]);
    }
}
