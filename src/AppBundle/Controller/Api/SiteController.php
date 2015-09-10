<?php

namespace Undine\AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Undine\Api\Command\SiteConnectCommand;
use Undine\Api\Result\SiteConnectResult;
use Undine\Api\Result\SiteLoginResult;
use Undine\Api\Result\SiteLogoutResult;
use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\ApiCommand;
use Undine\Configuration\ApiResult;
use Undine\Event\Events;
use Undine\Event\SiteDisconnectEvent;
use Undine\Model\Site;
use Undine\Oxygen\Action\ModuleDisableAction;
use Undine\Oxygen\Action\SiteLogoutAction;
use Undine\Oxygen\Action\SitePingAction;
use Undine\Oxygen\Reaction\SiteLogoutReaction;

class SiteController extends AppController
{
    /**
     * @Route("site.connect", name="api-site.connect")
     * @ApiCommand("api__site_connect")
     * @ApiResult()
     */
    public function connectAction(SiteConnectCommand $command)
    {
        list($privateKey, $publicKey) = \Undine\Functions\openssl_generate_rsa_key_pair();
        $site = new Site($command->getUrl(), $this->getUser(), $privateKey, $publicKey);

        $this->oxygenClient->send($site, new SitePingAction());

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
        $this->oxygenClient->send($site, new SitePingAction());

        return new SiteConnectResult($site);
    }

    /**
     * @Route("site.disconnect", name="api-site.disconnect")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
     * @ApiResult()
     */
    public function disconnectAction(Site $site)
    {
        $this->oxygenClient->send($site, new ModuleDisableAction(['oxygen']));

        $this->dispatcher->dispatch(new SiteDisconnectEvent($site), Events::SITE_DISCONNECT);

        // @todo: Chain extensions/updates/etc. removal.
        $this->em->remove($site);
        $this->em->flush($site);

        return new SiteConnectResult($site);
    }

    /**
     * @Route("site.login", name="api-site.login")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
     * @ApiResult()
     */
    public function loginAction(Site $site)
    {
        $loginUrl = $this->oxygenLoginUrlGenerator->generateUrl($site, $this->getUser()->getUid());

        return new SiteLoginResult($site, $loginUrl);
    }

    /**
     * @Route("site.logout", name="api-site.logout")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
     * @ApiResult()
     */
    public function logoutAction(Site $site)
    {
        /** @var SiteLogoutReaction $reaction */
        $reaction = $this->oxygenClient->send($site, new SiteLogoutAction($this->getUser()->getUid()));

        return new SiteLogoutResult($site, $reaction->getDestroyedSessions());
    }
}
