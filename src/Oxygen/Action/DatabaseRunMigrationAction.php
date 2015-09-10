<?php

namespace Undine\Oxygen\Action;

class DatabaseRunMigrationAction extends AbstractAction
{
    /**
     * @var string
     */
    private $module;

    /**
     * @var int
     */
    private $number;

    /**
     * @var array
     */
    private $dependencyMap;

    /**
     * @param string $module
     * @param int    $number
     * @param array  $dependencyMap
     */
    public function __construct($module, $number, array $dependencyMap)
    {
        $this->module        = $module;
        $this->number        = $number;
        $this->dependencyMap = $dependencyMap;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'database.runMigration';
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return [
            'module'        => $this->module,
            'number'        => $this->number,
            'dependencyMap' => $this->dependencyMap,
        ];
    }
}
