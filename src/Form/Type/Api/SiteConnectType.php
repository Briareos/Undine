<?php

namespace Undine\Form\Type\Api;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;
use Undine\Api\Command\SiteConnectCommand;
use Undine\Api\Error\Site\UrlEmpty;
use Undine\Api\Error\Site\UrlInvalid;
use Undine\Api\Error\Site\UrlTooLong;
use Undine\Api\Error\SiteConstraint;
use Undine\Form\Transformer\StringToUriTransformer;
use Undine\Model\Site\FtpCredentials;

class SiteConnectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', UrlType::class, [
            'constraints'     => [
                new NotBlank([
                    'message' => new UrlEmpty(),
                ]),
                new Url([
                    'message' => new UrlInvalid(),
                ]),
                new Length([
                    'max'        => 255,
                    'maxMessage' => new UrlTooLong(),
                ]),
            ],
            'invalid_message' => new UrlInvalid(),
        ]);
        $builder->get('url')->addViewTransformer(new StringToUriTransformer());

        $builder->add('checkUrl', CheckboxType::class);
        $builder->add('httpUsername', TextType::class);
        $builder->add('httpPassword', TextType::class);
        $builder->add('adminUsername', TextType::class);
        $builder->add('adminPassword', TextType::class);
        $builder->add('ftpMethod', ChoiceType::class, [
            'choices' => [
                FtpCredentials::METHOD_FTP => 'FTP',
                FtpCredentials::METHOD_SSH => 'SSH',
            ],
        ]);
        $builder->add('ftpUsername', TextType::class);
        $builder->add('ftpPassword', TextType::class);
        $builder->add('ftpHost', TextType::class);
        $builder->add('ftpPort', TextType::class, [
            'constraints' => [
                new Regex([
                    'pattern' => '/^\d+$/',
                ]),
            ],
        ]);
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
                    $form->get('checkUrl')->getData(),
                    $form->get('httpUsername')->getData(),
                    $form->get('httpPassword')->getData(),
                    $form->get('adminUsername')->getData(),
                    $form->get('adminPassword')->getData(),
                    $form->get('ftpMethod')->getData(),
                    $form->get('ftpUsername')->getData(),
                    $form->get('ftpPassword')->getData(),
                    $form->get('ftpHost')->getData(),
                    $form->get('ftpPort')->getData()
                );
            },
        ]);
    }
}
