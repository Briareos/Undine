<?php

namespace Undine\Form\Transformer;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToUriTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if ($value === null) {
            return '';
        }

        if (!$value instanceof UriInterface) {
            throw new TransformationFailedException(sprintf('The "%s" expects an instance of "%s".', __CLASS__, UriInterface::class));
        }

        return (string)$value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($value === '') {
            return null;
        }

        try {
            return new Uri($value);
        } catch (\InvalidArgumentException$e) {
            throw new TransformationFailedException($e->getMessage());
        }
    }
}
