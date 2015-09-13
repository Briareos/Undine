<?php

namespace Undine\Oxygen\State;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Undine\Event\Events;
use Undine\Event\SiteStateResultEvent;
use Undine\Model\Site;
use Undine\Model\Site\SiteState;
use Undine\Model\SiteExtension;
use Undine\Model\SiteUpdate;

class SiteStateResultTracker
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Returns a list of parameters that should be passed to the Oxygen module's AttachStateListener.
     * For example, this method attaches 'extensionsChecksum' property, and if it does not match with the 'system' table checksum
     * on the Drupal site that the Oxygen module is on, the module returns full table as a result, and the new checksum.
     *
     * @param Site $site
     *
     * @return array
     */
    public function getParameters(Site $site)
    {
        return [
            'extensionsChecksum' => $site->getSiteState()->getExtensionsChecksum(),
        ];
    }

    /**
     * Creates a new site state.
     *
     * @param Site  $site
     * @param array $result The resulting state returned from the Oxygen module.
     */
    public function setResult(Site $site, array $result)
    {
        $stateData = $this->getRootResolver()->resolve($result);
        foreach ($stateData['extensions'] as &$extensionData) {
            $extensionData = new SiteExtensionResult($this->getExtensionResolver()->resolve($extensionData));
        }
        foreach ($stateData['updates'] as &$updateData) {
            $updateData = new SiteUpdateResult($this->getUpdateResolver()->resolve($updateData));
        }
        foreach ($stateData as $key => $value) {
            // @TODO think of a better way to do safe-guard against long data.
            if (is_string($value) && mb_strlen($value) > 255) {
                throw new \RuntimeException(sprintf('The string value "%s" for key "%s" is too long.', $value, $key));
            }
            if (is_int($value) && strlen($value) > 10) {
                throw new \RuntimeException(sprintf('The numeric value "%s" for key "%s" is too long.', $value, $key));
            }
        }

        $siteStateResult = new SiteStateResult($stateData);
        $event           = new SiteStateResultEvent($site, $siteStateResult);
        $this->dispatcher->dispatch(Events::SITE_STATE_RESULT, $event);
    }

    /**
     * @return OptionsResolver
     */
    private function getRootResolver()
    {
        static $resolver;

        if ($resolver === null) {
            $allowedTimezones = \DateTimeZone::listIdentifiers();
            // Allow empty timezone.
            $allowedTimezones[] = '';

            $resolver = (new OptionsResolver())
                ->setRequired(['siteKey', 'cronKey', 'cronLastRunAt', 'siteMail', 'siteName', 'siteRoot', 'drupalRoot', 'drupalVersion', 'drupalMajorVersion', 'updatesLastCheckAt', 'timezone', 'phpVersion', 'phpVersionId', 'databaseDriver', 'databaseDriverVersion', 'databaseTablePrefix', 'memoryLimit', 'processArchitecture', 'internalIp', 'uname', 'hostname', 'os', 'windows', 'extensionsChecksum', 'extensionsCacheHit', 'extensions', 'updates'])
                ->setAllowedTypes('siteKey', 'string')
                ->setAllowedTypes('cronKey', 'string')
                ->setAllowedTypes('cronLastRunAt', 'int')
                /** @noinspection PhpUnusedParameterInspection */
                ->setNormalizer('cronLastRunAt', function (OptionsResolver $resolver, $timestamp) {
                    return new \DateTime('@'.$timestamp);
                })
                ->setAllowedTypes('siteMail', 'string')
                ->setAllowedTypes('siteName', 'string')
                ->setAllowedTypes('siteRoot', 'string')
                ->setAllowedTypes('drupalRoot', 'string')
                ->setAllowedTypes('drupalVersion', 'string')
                ->setAllowedValues('drupalMajorVersion', [7, 8])
                ->setAllowedTypes('updatesLastCheckAt', 'int')
                /** @noinspection PhpUnusedParameterInspection */
                ->setNormalizer('updatesLastCheckAt', function (OptionsResolver $resolver, $timestamp) {
                    return new \DateTime('@'.$timestamp);
                })
                ->setAllowedValues('timezone', $allowedTimezones)
                /** @noinspection PhpUnusedParameterInspection */
                ->setNormalizer('timezone', function (OptionsResolver $resolver, $timezone) {
                    if ($timezone === '') {
                        return null;
                    }

                    return new \DateTimeZone($timezone);
                })
                ->setAllowedTypes('phpVersion', 'string')
                ->setAllowedTypes('phpVersionId', 'int')
                ->setAllowedValues('databaseDriver', [
                    SiteState::DATABASE_DRIVER_MYSQL,
                    SiteState::DATABASE_DRIVER_PGSQL,
                    SiteState::DATABASE_DRIVER_SQLITE,
                ])
                ->setAllowedTypes('databaseDriverVersion', 'string')
                ->setAllowedTypes('databaseTablePrefix', 'string')
                ->setAllowedTypes('memoryLimit', 'int')
                ->setAllowedValues('processArchitecture', [32, 64])
                ->setAllowedTypes('internalIp', 'string')
                ->setAllowedTypes('uname', 'string')
                ->setAllowedTypes('hostname', 'string')
                ->setAllowedTypes('os', 'string')
                ->setAllowedTypes('windows', 'bool')
                ->setAllowedTypes('extensionsChecksum', 'string')
                ->setAllowedTypes('extensionsCacheHit', 'bool')
                ->setAllowedTypes('extensions', 'array')
                ->setAllowedTypes('updates', 'array');
        }

        return $resolver;
    }

    private function getExtensionResolver()
    {
        static $resolver;

        if ($resolver === null) {
            $resolver = (new OptionsResolver())
                ->setRequired(['filename', 'type', 'slug', 'parent', 'enabled', 'name', 'description', 'package', 'version', 'required', 'dependencies', 'project'])
                ->setAllowedTypes('filename', 'string')
                ->setAllowedValues('type', [SiteExtension::TYPE_MODULE, SiteExtension::TYPE_THEME])
                ->setAllowedTypes('slug', 'string')
                ->setAllowedTypes('parent', ['null', 'string'])
                ->setAllowedTypes('enabled', 'bool')
                ->setAllowedTypes('name', 'string')
                ->setAllowedTypes('description', 'string')
                ->setAllowedTypes('package', ['null', 'string'])
                ->setAllowedTypes('version', ['null', 'string'])
                ->setAllowedTypes('required', 'bool')
                ->setAllowedTypes('dependencies', 'array')
                /** @noinspection PhpUnusedParameterInspection */
                ->setNormalizer('dependencies', $this->createStringNormalizer('dependencies'))
                ->setAllowedTypes('project', ['null', 'string']);
        }

        return $resolver;
    }

    private function getUpdateResolver()
    {
        static $resolver;

        if ($resolver === null) {
            $resolver = (new OptionsResolver())
                ->setRequired(['slug', 'type', 'name', 'project', 'package', 'existingVersion', 'recommendedVersion', 'recommendedDownloadLink', 'status', 'includes', 'enabled', 'baseThemes', 'subThemes'])
                ->setAllowedTypes('slug', 'string')
                ->setAllowedValues('type', [SiteUpdate::TYPE_CORE, SiteUpdate::TYPE_MODULE, SiteUpdate::TYPE_THEME])
                ->setAllowedTypes('name', 'string')
                ->setAllowedTypes('project', ['null', 'string'])
                ->setAllowedTypes('package', ['null', 'string'])
                ->setAllowedTypes('existingVersion', 'string')
                ->setAllowedTypes('recommendedVersion', 'string')
                ->setAllowedTypes('recommendedDownloadLink', 'string')
                ->setAllowedValues('status', SiteUpdate::getStatuses())
                ->setAllowedTypes('includes', 'array')
                ->setNormalizer('includes', $this->createStringNormalizer('includes'))
                ->setAllowedTypes('enabled', 'bool')
                ->setAllowedTypes('baseThemes', 'array')
                ->setNormalizer('baseThemes', $this->createStringNormalizer('baseThemes'))
                ->setAllowedTypes('subThemes', 'array')
                ->setNormalizer('subThemes', $this->createStringNormalizer('subThemes'));
        }

        return $resolver;
    }

    /**
     * @param string $optionName
     *
     * @return callable
     */
    private function createStringNormalizer($optionName)
    {
        /** @noinspection PhpUnusedParameterInspection */
        /** @noinspection PhpDocSignatureInspection */
        return function (OptionsResolver $resolver, array $values) use ($optionName) {
            foreach ($values as $value) {
                if (!is_string($value)) {
                    throw new \InvalidArgumentException(sprintf('Option "%s" is expected to hold only strings.', $optionName));
                }
            }

            return $values;
        };
    }
}
