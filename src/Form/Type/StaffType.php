<?php

namespace Undine\Form\Type;

use Undine\Model\Staff;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;

class StaffType extends AbstractType
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getName()
    {
        return 'staff';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email');
        $builder->add('plainPassword', 'password', [
            'constraints' => [
                new Length([
                    // Bcrypt limitation.
                    'max' => 72,
                ]),
            ],
        ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var Staff $staff */
            $staff = $event->getData();
            if ($staff->getPlainPassword() === null) {
                return;
            }
            $password = $this->passwordEncoder->encodePassword($staff, $staff->getPlainPassword());
            $staff->setPassword($password);
        }, 10); // Priority before the validation.
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Staff::class,
            'empty_data' => function (FormInterface $form) {
                return new Staff($form->get('email')->getData(), null);
            },
        ]);
    }
}
