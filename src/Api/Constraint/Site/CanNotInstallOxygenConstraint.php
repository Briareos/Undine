<?php

namespace Undine\Api\Constraint\Site;

use Undine\Api\Constraint\AbstractConstraint;

class CanNotInstallOxygenConstraint extends AbstractConstraint
{
    const STEP_LIST_MODULES = 'list_modules';
    const STEP_SEARCH_UPDATE_MODULE = 'search_update_module';
    const STEP_SEARCH_OXYGEN_MODULE = 'search_oxygen_module';

    /**
     * @var string
     */
    private $step;

    /**
     * @param string $step
     */
    public function __construct($step)
    {
        $this->step = $step;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'site.can_not_install_oxygen';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'step' => $this->step,
        ];
    }
}
