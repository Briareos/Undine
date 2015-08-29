<?php

namespace Undine\Api\Command;

class ModuleEnableCommand
{
    /**
     * @var string
     */
    private $module;

    /**
     * @var bool
     */
    private $enableDependencies;

    /**
     * @param string $module
     * @param bool   $enableDependencies
     */
    public function __construct($module, $enableDependencies)
    {
        $this->module             = $module;
        $this->enableDependencies = $enableDependencies;
    }

    /**
     * @return string[]
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return bool
     */
    public function shouldEnableDependencies()
    {
        return $this->enableDependencies;
    }
}
