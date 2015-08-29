<?php

namespace Undine\Api\Command;

class ModuleDisableCommand
{
    /**
     * @var string
     */
    private $module;

    /**
     * @var bool
     */
    private $disableDependents;

    /**
     * @param string $module
     * @param bool   $disableDependents
     */
    public function __construct($module, $disableDependents)
    {
        $this->module            = $module;
        $this->disableDependents = $disableDependents;
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
    public function shouldDisableDependents()
    {
        return $this->disableDependents;
    }
}
