<?php

namespace Undine\Api\Transformer;

use League\Fractal\TransformerAbstract;

class TransformerRegistry
{
    /**
     * @var TransformerAbstract[]
     */
    private $registry = [];

    /**
     * @param string              $name
     * @param TransformerAbstract $transformer
     */
    public function set($name, TransformerAbstract $transformer)
    {
        $this->registry[$name] = $transformer;
    }

    /**
     * @param string $name
     *
     * @return TransformerAbstract
     *
     * @throws \OutOfBoundsException If the transformer does not exist.
     */
    public function get($name)
    {
        if (!isset($this->registry[$name])) {
            throw new \OutOfBoundsException(sprintf('The transformer named "%s" does not exist.', $name));
        }

        return $this->registry[$name];
    }
}
