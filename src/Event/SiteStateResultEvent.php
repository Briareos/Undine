<?php

namespace Undine\Event;

use Symfony\Component\EventDispatcher\Event;
use Undine\Model\Site;
use Undine\Oxygen\State\SiteStateResult;

class SiteStateResultEvent extends Event
{
    /**
     * @var Site
     */
    private $site;

    /**
     * @var SiteStateResult
     */
    private $stateResult;

    /**
     * @param Site            $site
     * @param SiteStateResult $stateResult
     */
    public function __construct(Site $site, SiteStateResult $stateResult)
    {
        $this->site        = $site;
        $this->stateResult = $stateResult;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return SiteStateResult
     */
    public function getSiteStateResult()
    {
        return $this->stateResult;
    }
}
