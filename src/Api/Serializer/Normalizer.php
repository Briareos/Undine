<?php

namespace Undine\Api\Serializer;

use Doctrine\Common\Util\ClassUtils;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Undine\Api\Transformer\TransformerRegistry;

/**
 * The normalizer service should only be used on object instances, not scalar values.
 */
class Normalizer
{
    /**
     * @var Manager
     */
    private $manager;
    /**
     * @var TransformerRegistry
     */
    private $transformerRegistry;

    /**
     * @param Manager             $manager
     * @param TransformerRegistry $transformerRegistry
     */
    public function __construct(Manager $manager, TransformerRegistry $transformerRegistry)
    {
        $this->manager             = $manager;
        $this->transformerRegistry = $transformerRegistry;
    }

    /**
     * @param object  $object
     * @param Context $context
     *
     * @return array
     */
    public function normalizeObject($object, Context $context)
    {
        $class = ClassUtils::getClass($object);

        // "Includes" are stateful, so make sure they are always explicitly set.
        $this->manager->parseIncludes($context->getIncludesAsString());

        return $this->manager->createData(new Item($object, $this->transformerRegistry->get($class)))->toArray();
    }

    /**
     * @param object[] $objects All instances are expected to be of the same class.
     * @param Context  $context
     *
     * @return array
     */
    public function normalizeObjectCollection(array $objects, Context $context)
    {
        if (count($objects) === 0) {
            return [];
        }

        $class = ClassUtils::getClass(reset($object));

        foreach ($objects as $object) {
            if (!is_object($object)) {
                throw new \InvalidArgumentException(sprintf('All arguments are expected to be instances of objects, "%s" found.', gettype($object)));
            }
            if (!$object instanceof $class) {
                throw new \InvalidArgumentException(sprintf('All objects are expected to be an instance of "%s", an instance of "%s" found.', $class, ClassUtils::getClass($object)));
            }
        }

        // "Includes" are stateful, so make sure they are always explicitly set.
        $this->manager->parseIncludes($context->getIncludesAsString());

        return $this->manager->createData(new Collection($objects, $this->transformerRegistry->get($class)))->toArray();
    }

    /**
     * @param string $class
     *
     * @return TransformerAbstract
     */
    public function getNormalizer($class)
    {
        return $this->transformerRegistry->get($class);
    }
}
