<?php

namespace Undine\Form\Type\Admin;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Undine\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {

        $this->passwordEncoder = $passwordEncoder;
    }

    public function getName()
    {
        return 'admin__user';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
            'constraints' => [
                new NotBlank(['groups' => ['create']]),
            ],
        ]);
        $builder->add('email', 'email', [
            'constraints' => [
                new NotBlank(['groups' => ['create']]),
            ],
        ]);
        $builder->add('plainPassword', 'password', [
            'constraints' => [
                new NotBlank(['groups' => ['create']]),
                new Length([
                    // Bcrypt limitation
                    'max' => 72,
                ]),
            ],
            'required'    => false,
        ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var User $user */
            $user = $event->getData();
            if ($user->getPlainPassword() === null) {
                return;
            }
            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
        }, 10); // Priority before the validation.
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'empty_data' => function (FormInterface $form) {
                return new User($form->get('name')->getData(), $form->get('email')->getData());
            },
        ]);
    }
}
