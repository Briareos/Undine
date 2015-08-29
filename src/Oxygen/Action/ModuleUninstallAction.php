<?php

namespace Undine\Oxygen\Action;

class ModuleUninstallAction extends AbstractAction
{
    /**
     * @var string[]
     */
    private $modules;

    /**
     * @var bool
     */
    private $uninstallDependents;

    /**
     * @param string[] $modules
     * @param bool     $uninstallDependents
     */
    public function __construct(array $modules, $uninstallDependents = true)
    {
        $this->modules             = $modules;
        $this->uninstallDependents = $uninstallDependents;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'module.uninstall';
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return [
            'modules'             => $this->modules,
            'uninstallDependents' => $this->uninstallDependents,
        ];
    }
}
