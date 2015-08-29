<?php

namespace Undine\Form\Type\Web;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Undine\Repository\UserRepository;
use Undine\Web\Command\RegistrationCommand;

class RegistrationType extends AbstractType
{
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'web__registration';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', [
            'constraints' => [
                new Type(['type' => 'string']),
                new NotBlank(),
            ],
        ]);
        $builder->add('email', 'email', [
            'constraints' => [
                new Type(['type' => 'string']),
                new NotBlank(),
            ],
        ]);
        $builder->add('plainPassword', 'password', [
            'constraints' => [
                new Type(['type' => 'string']),
                new NotBlank(),
            ],
        ]);

        // We can't use UniqueEntity validator because it must be tied to the 'data' object.
        // Regardless, this is quite alright, because this is only used for new user creation, not modification.
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var RegistrationCommand $command */
            $command = $event->getData();
            $email   = $command->getEmail();
            // This is user-submitted data, and it might be an array.
            // Even if validation fails, this will still get called.
            if (!is_string($email)) {
                return;
            }
            $users = $this->userRepository->findBy(['email' => $email]);
            if (!$users) {
                return;
            }
            $event->getForm()->addError(new FormError('A user with the specified email address already exists.'));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RegistrationCommand::class,
            'empty_data' => function (FormInterface $form) {
                return new RegistrationCommand($form->get('name')->getData(), $form->get('email')->getData(), $form->get('plainPassword')->getData());
            },
        ]);
    }
}