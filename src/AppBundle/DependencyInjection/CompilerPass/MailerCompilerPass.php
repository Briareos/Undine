<?php

namespace Undine\AppBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MailerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $emailFactory = $container->getDefinition('undine.email.factory');

        foreach ($container->findTaggedServiceIds('app.email') as $id => list($tag)) {
            $emailFactory->addMethodCall('registerEmail', [$tag['alias'], new Reference($id)]);
        }
    }
}
