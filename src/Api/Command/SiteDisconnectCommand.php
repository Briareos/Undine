<?php

namespace Undine\Api\Command;

use Undine\Model\Site;

class SiteDisconnectCommand extends AbstractCommand
{
    /**
     * @var Site
     */
    private $site;

    /**
     * @param Site $site
     */
    public function __construct($site)
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
