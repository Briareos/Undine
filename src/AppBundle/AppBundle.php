<?php

namespace Undine\AppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Undine\AppBundle\DependencyInjection\CompilerPass\TwigEnvironmentPass;
use Undine\Model\Site;
use Undine\Model\User;

class AppBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TwigEnvironmentPass());
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->registerUidInfo();
    }

    /**
     * Registers UID seeds used by models that implement the UidInterface.
     *
     * @see UidInterface
     */
    private function registerUidInfo()
    {
        if (isset($GLOBALS['uid_registry'])) {
            // Might be inside a test case.
            return;
        }

        // The values are set in this way to not auto-load them if not needed.
        $GLOBALS['uid_registry'] = [
            User::class => [
                'U',
                1604604479,
                2022350271,
                357902060,
            ],
            Site::class => [
                'S',
                695586097,
                1192258513,
                540361865,
            ],
        ];
    }
}
