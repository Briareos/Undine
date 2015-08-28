<?php

namespace Undine\AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class AppExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator($container->getParameter('kernel.root_dir') . '/Resources/config/services'));
        $loader->load('services.xml');
        $loader->load('listeners.xml');
        $loader->load('api_types.xml');
        $loader->load('web_types.xml');
        $loader->load('type_extensions.xml');
        $loader->load('admin_types.xml');
        $loader->load('security.xml');
        $loader->load('validators.xml');
        $loader->load('repositories.xml');
        $loader->load('twig.xml');
        $loader->load('emails.xml');
        $loader->load('serializers.xml');
        $loader->load('menu.xml');
        $loader->load('oxygen.xml');
    }
}
