<?php

namespace Undine\EventListener;

use Doctrine\ORM\EntityManager;
use Undine\Event\SiteStateResultEvent;
use Undine\Model\SiteState;
use Undine\Model\SiteExtension;
use Undine\Model\SiteUpdate;
use Undine\Oxygen\State\Result\SiteStateResult;

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
        $state  = $event->getSite()->getSiteState();
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
            ->setUpdatesLastCheckAt($result->updatesLastCheckAt)
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

        // Initialize the extension arrays here we don't have to check twice if we should persist the entities below.
        $stateExtensions = $deletedExtensions = [];
        if (!$result->extensionsCacheHit) {
            // Extensions are indexed by their slug, which is part of their primary key (second part is site's ID).
            $stateExtensions    = $this->getExtensions($state, $result);
            $deletedExtensions = array_diff_key($state->getSiteExtensions(), $stateExtensions);
            $state->setSiteExtensions($stateExtensions);

            $state->setExtensionsChecksum($result->extensionsChecksum);
        }

        // @todo: Check for some kind of update cache hit?
        $siteUpdates    = $this->getUpdates($state, $result);
        $deletedUpdates = array_diff_key($state->getSiteUpdates(), $siteUpdates);
        $state->setSiteUpdates($siteUpdates);


        if ($this->em->getUnitOfWork()->isInIdentityMap($state) && !$this->em->getUnitOfWork()->isScheduledForInsert($state)) {
            array_walk($stateExtensions, [$this->em, 'persist']);
            array_walk($deletedExtensions, [$this->em, 'remove']);
            array_walk($siteUpdates, [$this->em, 'persist']);
            array_walk($deletedUpdates, [$this->em, 'remove']);
            $this->em->persist($state);
            $this->em->flush();
        }
    }

    /**
     * @param SiteState       $state
     * @param SiteStateResult $result
     *
     * @return SiteExtension[]
     */
    private function getExtensions(SiteState $state, SiteStateResult $result)
    {
        if (!$result->extensions) {
            return [];
        }

        $existingExtensions = $state->getSiteExtensions();
        $extensions         = [];
        foreach ($result->extensions as $slug => $extensionData) {
            if (isset($existingExtensions[$slug])) {
                $extension = $existingExtensions[$slug];
            } else {
                $extension = new SiteExtension($state, $slug);
            }
            $extension->setFilename($extensionData->filename)
                ->setType($extensionData->type)
                ->setParent($extensionData->parent)
                ->setEnabled($extensionData->enabled)
                ->setName($extensionData->name)
                ->setDescription($extensionData->description)
                ->setPackage($extensionData->package)
                ->setVersion($extensionData->version)
                ->setRequired($extensionData->required)
                ->setDependencies($extensionData->dependencies)
                ->setProject($extensionData->project);

            $extensions[$slug] = $extension;
        }

        return $extensions;
    }

    /**
     * @param SiteState            $state
     * @param SiteStateResult $result
     *
     * @return SiteUpdate[]
     */
    private function getUpdates(SiteState $state, SiteStateResult $result)
    {
        if (!$result->updates) {
            return [];
        }

        $existingUpdates = $state->getSiteUpdates();
        $updates         = [];
        foreach ($result->updates as $slug => $updateData) {
            if (isset($existingUpdates[$slug])) {
                $update = $existingUpdates[$slug];
            } else {
                $update = new SiteUpdate($state, $slug);
            }
            $update->setType($updateData->type)
                ->setName($updateData->name)
                ->setProject($updateData->project)
                ->setPackage($updateData->package)
                ->setExistingVersion($updateData->existingVersion)
                ->setRecommendedVersion($updateData->recommendedVersion)
                ->setRecommendedDownloadLink($updateData->recommendedDownloadLink)
                ->setStatus($updateData->status)
                ->setIncludes($updateData->includes)
                ->setEnabled($updateData->enabled)
                ->setBaseThemes($updateData->baseThemes)
                ->setSubThemes($updateData->subThemes);

            $updates[$slug] = $update;
        }

        return $updates;
    }
}
