<?php

namespace Undine\Oxygen\Action;

class ModuleEnableAction extends AbstractAction
{
    /**
     * @var string[]
     */
    private $modules;

    /**
     * @var bool
     */
    private $enableDependencies;

    /**
     * @param string[] $modules
     * @param bool     $enableDependencies
     */
    public function __construct(array $modules, $enableDependencies = false)
    {
        $this->modules            = $modules;
        $this->enableDependencies = $enableDependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'module.disable';
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return [
            'modules'            => $this->modules,
            'enableDependencies' => $this->enableDependencies,
        ];
    }
}
