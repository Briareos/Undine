<?php

namespace Undine\Event;

use Symfony\Component\EventDispatcher\Event;
use Undine\Model\Site;

class SiteConnectEvent extends Event
{
    /**
     * @var Site
     */
    private $site;

    /**
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }
}
