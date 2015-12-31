<?php

namespace Undine\AppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Undine\AppBundle\DependencyInjection\CompilerPass\ApiSerializationPass;
use Undine\AppBundle\DependencyInjection\CompilerPass\MailerCompilerPass;
use Undine\AppBundle\DependencyInjection\CompilerPass\TwigEnvironmentPass;
use Undine\AppBundle\DependencyInjection\CompilerPass\ValidationPass;
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
        $container->addCompilerPass(new ApiSerializationPass());
        $container->addCompilerPass(new ValidationPass());
        $container->addCompilerPass(new MailerCompilerPass());
    }
}
