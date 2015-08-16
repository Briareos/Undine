<?php

namespace Undine\AppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Undine\AppBundle\DependencyInjection\CompilerPass\TwigEnvironmentPass;

class AppBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TwigEnvironmentPass());
    }
}
