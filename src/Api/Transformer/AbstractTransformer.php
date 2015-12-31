<?php

namespace Undine\Api\Transformer;

use League\Fractal\TransformerAbstract;

abstract class AbstractTransformer extends TransformerAbstract
{
    /**
     * @var TransformerRegistry
     */
    protected $transformers;

    /**
     * @param TransformerRegistry $registry
     */
    public function setRegistry(TransformerRegistry $registry)
    {
        $this->transformers = $registry;
    }
}
