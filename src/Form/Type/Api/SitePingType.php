<?php

namespace Undine\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Undine\Api\Command\SitePingCommand;
use Undine\Api\Constraint\Site\NotFoundConstraint;
use Undine\Api\Constraint\Site\UidNotProvidedConstraint;
use Undine\Form\Transformer\UidToIdTransformer;
use Undine\Model\Site;

class SitePingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'api__site_ping';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('site', 'entity', [
            'class'           => Site::class,
            'invalid_message' => new NotFoundConstraint(),
            'constraints'     => [
                new NotBlank([
                    'message' => new UidNotProvidedConstraint(),
                ]),
            ],
        ]);
        $builder->get('site')->addViewTransformer(new UidToIdTransformer(Site::class));
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
