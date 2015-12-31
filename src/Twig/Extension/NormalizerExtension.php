<?php

namespace Undine\Twig\Extension;

use Undine\Api\Serializer\Context;
use Undine\Api\Serializer\Normalizer;

class NormalizerExtension extends \Twig_Extension
{
    /**
     * @var Normalizer
     */
    private $normalizer;

    /**
     * @param Normalizer $normalizer
     */
    public function __construct(Normalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'normalizer';
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('normalize_object', [$this, 'normalizeObject']),
            new \Twig_SimpleFilter('normalize_collection', [$this, 'normalizeCollection']),
        ];
    }

    /**
     * @param mixed
     *
     * @return array
     */
    public function normalizeObject($data)
    {
        $context = new Context();

        return $this->normalizer->normalizeObject($data, $context);
    }

    /**
     * @param mixed
     *
     * @return array
     */
    public function normalizeCollection($data)
    {
        $context = new Context();

        return $this->normalizer->normalizeObject($data, $context);
    }
}
