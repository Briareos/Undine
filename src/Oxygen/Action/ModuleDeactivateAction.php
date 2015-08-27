<?php

namespace Undine\Oxygen\Action;

class ModuleDeactivateAction extends AbstractAction
{
    /**
     * @var string[]
     */
    private $modules;

    /**
     * @param string[] $modules
     */
    public function __construct(array $modules)
    {
        $this->modules = $modules;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'module.deactivate';
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return [
            'modules' => $this->modules,
        ];
    }
}
