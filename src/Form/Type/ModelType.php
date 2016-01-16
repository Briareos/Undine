<?php

namespace Undine\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModelType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new class($this->em, $options['model']) implements DataTransformerInterface
        {
            /**
             * @var EntityManager
             */
            private $em;

            /**
             * @var string
             */
            private $model;

            public function __construct(EntityManager $em, $model)
            {
                $this->em = $em;
                $this->model = $model;
            }

            public function transform($value)
            {
                if ($value === null) {
                    return '';
                }

                if (!is_callable([$value, 'getId'])) {
                    throw new TransformationFailedException('The model must have a callable getId() method.');
                }

                return $value->getId();
            }

            public function reverseTransform($value)
            {
                if ($value === '') {
                    return;
                }

                if (!\Undine\Functions\valid_uuid($value)) {
                    throw new TransformationFailedException('Not a valid UUID.');
                }

                $model = $this->em->getRepository($this->model)->find($value);

                if ($model === null) {
                    throw new TransformationFailedException('Model with the specified UUID not found.');
                }

                return $model;
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('model');
        $resolver->setAllowedTypes('model', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }
}
