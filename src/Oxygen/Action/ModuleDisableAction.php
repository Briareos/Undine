<?php

namespace Undine\Oxygen\Action;

class ModuleDisableAction extends AbstractAction
{
    /**
     * @var string[]
     */
    private $modules;

    /**
     * @var bool
     */
    private $disableDependents;

    /**
     * @param string[] $modules
     * @param bool     $disableDependents
     */
    public function __construct(array $modules, $disableDependents = false)
    {
        $this->modules = $modules;
        $this->disableDependents = $disableDependents;
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
            'modules' => $this->modules,
            'disableDependents' => $this->disableDependents,
        ];
    }
}
