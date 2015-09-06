<?php

namespace Undine\EventListener;

use Doctrine\ORM\EntityManager;
use Undine\Event\SiteStateResultEvent;
use Undine\Model\Site;
use Undine\Model\SiteExtension;
use Undine\Oxygen\State\SiteStateResult;

class SiteStateResultListener
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onSiteStateResult(SiteStateResultEvent $event)
    {
        $site   = $event->getSite();
        $state  = $site->getSiteState();
        $result = $event->getSiteStateResult();

        $state->setSiteKey($result->siteKey)
            ->setCronKey($result->cronKey)
            ->setCronLastRunAt($result->cronLastRunAt)
            ->setSiteName($result->siteName)
            ->setSiteMail($result->siteMail)
            ->setSiteRoot($result->siteRoot)
            ->setDrupalRoot($result->drupalRoot)
            ->setDrupalVersion($result->drupalVersion)
            ->setDrupalMajorVersion($result->drupalMajorVersion)
            ->setUpdateLastCheckAt($result->updateLastCheckAt)
            ->setTimezone($result->timezone)
            ->setPhpVersion($result->phpVersion)
            ->setPhpVersionId($result->phpVersionId)
            ->setDatabaseDriver($result->databaseDriver)
            ->setDatabaseDriverVersion($result->databaseDriverVersion)
            ->setDatabaseTablePrefix($result->databaseTablePrefix)
            ->setMemoryLimit($result->memoryLimit)
            ->setProcessArchitecture($result->processArchitecture)
            ->setInternalIp($result->internalIp)
            ->setUname($result->uname)
            ->setHostname($result->hostname)
            ->setOs($result->os)
            ->setWindows($result->windows);

        if (!$result->extensionsCacheHit) {
            $extensions = $this->getExtensions($site, $result);
            array_walk($extensions, [$this->em, 'persist']);
            $site->setSiteExtensions($extensions);

            $state->setExtensionsChecksum($result->extensionsChecksum);
        }

        if ($this->em->getUnitOfWork()->isInIdentityMap($site) && !$this->em->getUnitOfWork()->isScheduledForInsert($site)) {
            $this->em->persist($site);
            $this->em->flush($site);
        }
    }

    /**
     * @param Site            $site
     * @param SiteStateResult $result
     *
     * @return SiteExtension[]
     */
    private function getExtensions(Site $site, SiteStateResult $result)
    {
        if (!$result->extensions) {
            return [];
        }

        $existingExtensions = $site->getSiteExtensions();
        $extensions         = [];
        foreach ($result->extensions as $extensionData) {
            if (isset($existingExtensions[$extensionData->slug])) {
                $extension = $existingExtensions[$extensionData->slug];
            } else {
                $extension = new SiteExtension($site, $extensionData->slug);
            }
            $extension->setFilename($extensionData->filename)
                ->setType($extensionData->type)
                ->setParent($extensionData->parent)
                ->setActive($extensionData->active)
                ->setName($extensionData->name)
                ->setDescription($extensionData->description)
                ->setPackage($extensionData->package)
                ->setVersion($extensionData->version)
                ->setRequired($extensionData->required)
                ->setDependencies($extensionData->dependencies)
                ->setProject($extensionData->project);

            $extensions[] = $extension;
        }

        return $extensions;
    }
}
