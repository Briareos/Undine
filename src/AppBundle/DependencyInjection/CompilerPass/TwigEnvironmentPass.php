<?php

namespace Undine\AppBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TwigEnvironmentPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('twig');

        foreach ($container->findTaggedServiceIds('twig.token_parser') as $id => $attributes) {
            $definition->addMethodCall('addTokenParser', [new Reference($id)]);
        }
    }
}
