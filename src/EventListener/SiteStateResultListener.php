<?php

namespace Undine\EventListener;

use Doctrine\ORM\EntityManager;
use Undine\Event\SiteStateResultEvent;

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

        $state->setSiteKey($result->siteKey);
        $state->setCronKey($result->cronKey);
        $state->setCronLastRunAt($result->cronLastRunAt);
        $state->setSiteName($result->siteName);
        $state->setSiteMail($result->siteMail);
        $state->setSiteRoot($result->siteRoot);
        $state->setDrupalRoot($result->drupalRoot);
        $state->setDrupalVersion($result->drupalVersion);
        $state->setUpdateLastCheckAt($result->updateLastCheckAt);
        $state->setTimezone($result->timezone);
        $state->setPhpVersion($result->phpVersion);
        $state->setPhpVersionId($result->phpVersionId);
        $state->setDatabaseDriver($result->databaseDriver);
        $state->setDatabaseDriverVersion($result->databaseDriverVersion);
        $state->setDatabaseTablePrefix($result->databaseTablePrefix);
        $state->setMemoryLimit($result->memoryLimit);
        $state->setProcessArchitecture($result->processArchitecture);
        $state->setInternalIp($result->internalIp);
        $state->setUname($result->uname);
        $state->setHostname($result->hostname);
        $state->setOs($result->os);
        $state->setWindows($result->windows);

        //$site->setSystemChecksum($state->systemChecksum);
        // @TODO: Set system data.

        if ($this->em->getUnitOfWork()->isInIdentityMap($site) && !$this->em->getUnitOfWork()->isScheduledForInsert($site)) {
            $this->em->persist($site);
            $this->em->flush($site);
        }
    }
}
