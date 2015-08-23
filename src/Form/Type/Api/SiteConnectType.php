<?php

namespace Undine\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Undine\Api\Command\SiteConnectCommand;
use Undine\Api\Constraint\SiteConstraint;
use Undine\Form\Transformer\StringToUriTransformer;

class SiteConnectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'api__site_connect';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', 'url', [
            'constraints'     => [
                new NotBlank([
                    'message' => SiteConstraint::URL_BLANK,
                ]),
                new Url([
                    'message'    => SiteConstraint::URL_INVALID,
                ]),
                new Length([
                    'max'        => 255,
                    'maxMessage' => SiteConstraint::URL_TOO_LONG,
                ]),
            ],
            'invalid_message' => SiteConstraint::URL_INVALID,
        ]);
        $builder->get('url')->addViewTransformer(new StringToUriTransformer());

        $builder->add('httpUsername', 'text');
        $builder->add('httpPassword', 'text');
        $builder->add('adminUsername', 'text');
        $builder->add('adminPassword', 'text');
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SiteConnectCommand::class,
            'empty_data' => function (FormInterface $form) {
                return new SiteConnectCommand(
                    $form->get('url')->getData(),
                    $form->get('httpUsername')->getData(),
                    $form->get('httpPassword')->getData(),
                    $form->get('adminUsername')->getData(),
                    $form->get('adminPassword')->getData()
                );
            },
        ]);
    }
}
