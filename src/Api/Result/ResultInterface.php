<?php

namespace Undine\Api\Result;

use Undine\Api\Serializer\Context;
use Undine\Api\Serializer\Normalizer;

interface ResultInterface
{
    /**
     * @param Normalizer $normalizer
     * @param Context    $context
     *
     * @return array
     */
    public function normalize(Normalizer $normalizer, Context $context);
}
