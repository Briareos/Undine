<?php

namespace Undine\Form\Transformer;

use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Undine\Uid\UidInterface;

class UidToIdTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var bool
     */
    private $compound;

    /**
     * @param UidInterface|string $class    Class name or an instance that implements UidInterface.
     * @param bool                $compound The transformer transforms an array instead of a single field.
     */
    public function __construct($class, $compound = false)
    {
        $this->class = is_object($class) ? ClassUtils::getClass($class) : $class;

        if (!class_implements($this->class, UidInterface::class)) {
            throw new \InvalidArgumentException(sprintf('The class "%s" must implement "%s" to use with this transformer.', $this->class, UidInterface::class));
        }

        $this->compound = $compound;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (!$value) {
            return $value;
        }

        $class = $this->class;

        if ($this->compound) {
            if (!is_array($value) && !$value instanceof \Traversable) {
                throw new TransformationFailedException('Excepted array or a \Traversable.');
            }

            return array_map([$class, 'getUidFromId'], $value);
        }

        return $class::getUidFromId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $class = $this->class;

        if ($this->compound) {
            if (!is_array($value) && !$value instanceof \Traversable) {
                throw new TransformationFailedException('Excepted array or a \Traversable.');
            }

            return array_map([$class, 'getIdFromUid'], $value);
        }

        return $class::getIdFromUid($value);
    }
}
