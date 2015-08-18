<?php

namespace Undine\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class ApiCommand extends ConfigurationAnnotation
{
    /**
     * Registered form type class name or alias.
     *
     * @var string
     */
    private $type;

    /**
     * Parameter name to inject into the controller action.
     * If it already exists (eg. injected by ParamConverter), it will be set as initial form data.
     *
     * @var string
     */
    protected $name = 'command';

    /**
     * Validation groups to use.
     *
     * @var string[]
     */
    protected $groups = [];

    public function setValue($value)
    {
        $this->type = $value;
    }

    public function getAliasName()
    {
        return 'api_command';
    }

    public function allowArray()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param string[] $groups
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;
    }
}
