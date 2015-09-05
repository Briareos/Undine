<?php

namespace Undine\Oxygen\State;

/**
 * @property string      $filename
 * @property string      $type
 * @property string      $slug
 * @property string|null $parent
 * @property bool        $status
 * @property string      $name
 * @property string      $description
 * @property string      $package
 * @property string      $version
 * @property bool        $required
 * @property string[]    $dependencies
 * @property string      $project
 */
class SiteExtensionResult
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
            throw new \OutOfBoundsException(sprintf('Property "%s" could not be found.'));
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
