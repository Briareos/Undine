<?php

namespace Undine\AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Undine\Api\Command\SiteConnectCommand;
use Undine\Api\Result\SiteConnectResult;
use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\ApiCommand;
use Undine\Configuration\ApiResult;
use Undine\Model\Site;

class SiteController extends AppController
{
    /**
     * @Route("site.connect", name="api-site.connect")
     * @ApiCommand("api__site_connect")
     * @ApiResult()
     */
    public function connectAction(SiteConnectCommand $command)
    {
        $site = new Site($command->getUrl(), $this->getUser());

        return new SiteConnectResult($site);
    }
}
