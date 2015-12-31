<?php

namespace Undine\Api\Command;

class ModuleUninstallCommand extends AbstractCommand
{
    /**
     * @var string
     */
    private $module;

    /**
     * @param string $module
     */
    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }
}
