<?php

namespace Undine\AppBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ApiSerializationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $registry = $container->getDefinition('undine.serializer.registry');

        foreach ($container->findTaggedServiceIds('serializer.transformer') as $id => $attributes) {
            if (empty($attributes[0]['alias'])) {
                throw new \RuntimeException('Every service tagged as "serializer.transformer" must have an "alias" attribute.');
            }
            $alias = $attributes[0]['alias'];
            if (!class_exists($alias)) {
                throw new \RuntimeException(sprintf('The "%s" serialization transformer requires class "%s" to exist, but it could not be found.', $id, $alias));
            }
            $registry->addMethodCall('set', [$alias, new Reference($id)]);
            $transformer = $container->getDefinition($id);
            $transformer->addMethodCall('setRegistry', [new Reference('undine.serializer.registry')]);
        }
    }
}
