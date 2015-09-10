<?php

namespace Undine\Oxygen\State;

trait StateResultTrait
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    function __get($name)
    {
        if (!array_key_exists($name, $this->data)) {
            throw new \OutOfBoundsException(sprintf('Property "%s" could not be found.', $name));
        }

        return $this->data[$name];
    }

    /**
     * {@inheritdoc}
     */
    function __isset($name)
    {
        return array_key_exists($this->data, $name);
    }
}
