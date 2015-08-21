<?php

namespace Undine\AppBundle\Controller\Api;

use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\ApiCommand;
use Undine\Configuration\ApiResult;

class SiteController extends AppController
{
    /**
     * @ApiCommand("api__site_connect")
     * @ApiResult()
     */
    public function connectAction(SiteConnectCommand $command)
    {
        return new SiteConnectResult();
    }
}
