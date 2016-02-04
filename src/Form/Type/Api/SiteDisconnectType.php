<?php

namespace Undine\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Undine\Api\Command\SiteDisconnectCommand;
use Undine\Api\Error as E;
use Undine\Form\Type\ModelType;
use Undine\Model\Site;

class SiteDisconnectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('site', ModelType::class, [
            'model' => Site::class,
            'invalid_message' => new E\Site\NotFound(),
            'constraints' => [
                new NotBlank([
                    'message' => new E\Site\IdNotProvided(),
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
            'data_class' => SiteDisconnectCommand::class,
            'empty_data' => function (FormInterface $form) {
                return new SiteDisconnectCommand($form->get('site')->getData());
            },
        ]);
    }
}
