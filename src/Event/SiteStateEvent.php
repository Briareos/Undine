<?php

namespace Undine\Event;

use Symfony\Component\EventDispatcher\Event;
use Undine\Model\Site;
use Undine\Oxygen\State\SiteState;

class SiteStateEvent extends Event
{
    /**
     * @var Site
     */
    private $site;

    /**
     * @var SiteState
     */
    private $state;

    /**
     * @param Site      $site
     * @param SiteState $state
     */
    public function __construct(Site $site, SiteState $state)
    {
        $this->site  = $site;
        $this->state = $state;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return SiteState
     */
    public function getState()
    {
        return $this->state;
    }
}
