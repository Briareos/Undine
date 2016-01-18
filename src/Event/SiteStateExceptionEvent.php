<?php

namespace Undine\Event;

use Symfony\Component\EventDispatcher\Event;
use Undine\Model\Site;

class SiteStateExceptionEvent extends Event
{
    /**
     * @var Site
     */
    private $site;

    /**
     * @var \Exception
     */
    private $exception;

    public function __construct(Site $site, \Exception $exception)
    {
        $this->site = $site;
        $this->exception = $exception;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
