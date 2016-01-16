<?php

namespace Undine\AppBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ValidationPass implements CompilerPassInterface
{
    /**
     * Load validation definitions from app/Resources/config/validation.
     *
     * @see FrameworkExtension::registerValidationConfiguration
     *
     * @param $container
     */
    public function process(ContainerBuilder $container)
    {
        $validatorBuilder = $container->getDefinition('validator.builder');

        list($xmlMappings, $yamlMappings) = $this->getValidatorMappingFiles($container);

        if (count($xmlMappings)) {
            $validatorBuilder->addMethodCall('addXmlMappings', [$xmlMappings]);
        }

        if (count($yamlMappings)) {
            $validatorBuilder->addMethodCall('addYamlMappings', [$yamlMappings]);
        }
    }

    /**
     * @see FrameworkExtension::getValidatorMappingFiles
     *
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function getValidatorMappingFiles(ContainerBuilder $container)
    {
        $files = [[], []];
        if (is_dir($dir = $container->getParameter('kernel.root_dir').'/Resources/config/validation')) {
            /** @var SplFileInfo $file */
            foreach (Finder::create()->files()->in($dir)->name('*.xml') as $file) {
                $files[0][] = $file->getRealpath();
            }
            foreach (Finder::create()->files()->in($dir)->name('*.yml') as $file) {
                $files[1][] = $file->getRealpath();
            }

            $container->addResource(new DirectoryResource($dir));
        }

        return $files;
    }
}
