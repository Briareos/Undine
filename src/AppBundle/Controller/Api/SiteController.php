<?php

namespace Undine\AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Undine\Api\Command\SiteConnectCommand;
use Undine\Api\Result\SiteConnectResult;
use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\ApiCommand;
use Undine\Configuration\ApiResult;
use Undine\Model\Site;
use Undine\Oxygen\Action\PingAction;

class SiteController extends AppController
{
    /**
     * @Route("site.connect", name="api-site.connect")
     * @ApiCommand("api__site_connect")
     * @ApiResult()
     */
    public function connectAction(SiteConnectCommand $command)
    {
        list($privateKey, $publicKey) = $this->get('undine.keychain_generator')->generateKeyPair();
        $site = new Site($command->getUrl(), $this->getUser(), $privateKey, $publicKey);

        $this->oxygenClient->send($site, new PingAction());

        $this->em->persist($site);
        $this->em->flush($site);

        return new SiteConnectResult($site);
    }

    /**
     * @Route("site.ping", name="api-site.ping")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
     * @ApiResult()
     */
    public function pingAction(Site $site)
    {
        $this->oxygenClient->send($site, new PingAction());

        $this->em->persist($site);
        $this->em->flush($site);

        return new SiteConnectResult($site);
    }
}
