<?php

namespace Undine\EventListener;

use Doctrine\ORM\EntityManager;
use Undine\Event\SiteStateEvent;

class SiteStateListener
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

    public function onSiteState(SiteStateEvent $event)
    {
        $site  = $event->getSite();
        $state = $event->getState();

        $site->setSiteKey($state->siteKey);
        $site->setCronKey($state->cronKey);
        $site->setCronLastRunAt($state->cronLastRunAt);
        $site->setSiteName($state->siteName);
        $site->setSiteMail($state->siteMail);
        $site->setSiteRoot($state->siteRoot);
        $site->setDrupalRoot($state->drupalRoot);
        $site->setDrupalVersion($state->drupalVersion);
        $site->setUpdateLastCheckAt($state->updateLastCheckAt);
        $site->setTimezone($state->timezone);
        $site->setPhpVersion($state->phpVersion);
        $site->setPhpVersionId($state->phpVersionId);
        $site->setDatabaseDriver($state->databaseDriver);
        $site->setDatabaseDriverVersion($state->databaseDriverVersion);
        $site->setDatabaseTablePrefix($state->databaseTablePrefix);
        $site->setMemoryLimit($state->memoryLimit);
        $site->setProcessArchitecture($state->processArchitecture);
        $site->setInternalIp($state->internalIp);
        $site->setUname($state->uname);
        $site->setHostname($state->hostname);
        $site->setOs($state->os);
        $site->setWindows($state->windows);

        //$site->setSystemChecksum($state->systemChecksum);
        // @TODO: Set system data.

        if (!$this->em->getUnitOfWork()->isScheduledForInsert($site)) {
            $this->em->persist($site);
            $this->em->flush($site);
        }
    }
}
