<?php

namespace Undine\Api\Error\DrupalClient;

use Undine\Api\Error\AbstractError;

class CanNotInstallOxygen extends AbstractError
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
    public static function getName()
    {
        return 'drupal_client.can_not_install_oxygen';
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
