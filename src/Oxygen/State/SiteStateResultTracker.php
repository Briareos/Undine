<?php

namespace Undine\Oxygen\State;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Undine\Event\Events;
use Undine\Event\SiteStateResultEvent;
use Undine\Model\Site;
use Undine\Model\Site\SiteState;

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
     * For example, this method attaches 'systemChecksum' property, and if it does not match with the 'system' table checksum
     * on the Drupal site that the Oxygen module is on, the module returns full table as a result, and the new checksum.
     *
     * @param Site $site
     *
     * @return array
     */
    public function getParameters(Site $site)
    {
        return [
            'systemChecksum' => $site->getSiteState()->getSystemChecksum(),
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
        $data = $this->getOptionsResolver()->resolve($result);
        foreach ($data as $key => $value) {
            // @TODO think of a better way to do safe-guard against long data.
            if (is_string($value) && mb_strlen($value) > 255) {
                throw new \RuntimeException(sprintf('The string value "%s" for key "%s" is too long.', $value, $key));
            }
            if (is_int($value) && strlen($value) > 10) {
                throw new \RuntimeException(sprintf('The numeric value "%s" for key "%s" is too long.', $value, $key));
            }
        }

        $siteStateResult = new SiteStateResult($data);
        $event           = new SiteStateResultEvent($site, $siteStateResult);
        $this->dispatcher->dispatch(Events::SITE_STATE_RESULT, $event);
    }

    /**
     * @return OptionsResolver
     */
    private function getOptionsResolver()
    {
        static $resolver;

        if ($resolver === null) {
            $resolver = new OptionsResolver();
            $resolver->setRequired(['siteKey', 'cronKey', 'cronLastRunAt', 'siteMail', 'siteName', 'siteRoot', 'drupalRoot', 'drupalVersion', 'updateLastCheckAt', 'timezone', 'phpVersion', 'phpVersionId', 'databaseDriver', 'databaseDriverVersion', 'databaseTablePrefix', 'memoryLimit', 'processArchitecture', 'internalIp', 'uname', 'hostname', 'os', 'windows', 'systemChecksum', 'systemCacheHit', 'systemData']);
            $resolver->addAllowedTypes('siteKey', 'string');
            $resolver->addAllowedTypes('cronKey', 'string');
            $resolver->addAllowedTypes('cronLastRunAt', 'int');
            /** @noinspection PhpUnusedParameterInspection */
            $resolver->setNormalizer('cronLastRunAt', function (OptionsResolver $resolver, $timestamp) {
                return new \DateTime('@'.$timestamp);
            });
            $resolver->addAllowedTypes('siteMail', 'string');
            $resolver->addAllowedTypes('siteName', 'string');
            $resolver->addAllowedTypes('siteRoot', 'string');
            $resolver->addAllowedTypes('drupalRoot', 'string');
            $resolver->addAllowedTypes('drupalVersion', 'string');
            $resolver->addAllowedTypes('updateLastCheckAt', 'int');
            /** @noinspection PhpUnusedParameterInspection */
            $resolver->setNormalizer('updateLastCheckAt', function (OptionsResolver $resolver, $timestamp) {
                return new \DateTime('@'.$timestamp);
            });
            $resolver->addAllowedValues('timezone', \DateTimeZone::listIdentifiers());
            /** @noinspection PhpUnusedParameterInspection */
            $resolver->setNormalizer('timezone', function (OptionsResolver $resolver, $timezone) {
                return new \DateTimeZone($timezone);
            });
            $resolver->addAllowedTypes('phpVersion', 'string');
            $resolver->addAllowedTypes('phpVersionId', 'int');
            $resolver->addAllowedValues('databaseDriver', [
                SiteState::DATABASE_DRIVER_MYSQL,
                SiteState::DATABASE_DRIVER_PGSQL,
                SiteState::DATABASE_DRIVER_SQLITE,
            ]);
            $resolver->addAllowedTypes('databaseDriverVersion', 'string');
            $resolver->addAllowedTypes('databaseTablePrefix', 'string');
            $resolver->addAllowedTypes('memoryLimit', 'int');
            $resolver->addAllowedValues('processArchitecture', [32, 64]);
            $resolver->addAllowedTypes('internalIp', 'string');
            $resolver->addAllowedTypes('uname', 'string');
            $resolver->addAllowedTypes('hostname', 'string');
            $resolver->addAllowedTypes('os', 'string');
            $resolver->addAllowedTypes('windows', 'bool');
            $resolver->addAllowedTypes('systemChecksum', 'string');
            $resolver->addAllowedTypes('systemCacheHit', 'bool');
            $resolver->addAllowedTypes('systemData', 'array');
        }

        return $resolver;
    }
}
