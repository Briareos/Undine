<?php

namespace Undine\Api\Result;

use Undine\Api\Serializer\Context;
use Undine\Api\Serializer\Normalizer;

abstract class AbstractResult implements ResultInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize(Normalizer $normalizer, Context $context)
    {
        return [];
    }
}
