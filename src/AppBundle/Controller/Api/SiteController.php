<?php

namespace Undine\AppBundle\Controller\Api;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\RejectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DomCrawler\Form;
use Undine\Api\Command\SiteConnectCommand;
use Undine\Api\Command\SitePingCommand;
use Undine\Api\Error\DrupalClient;
use Undine\Api\Error\Ftp;
use Undine\Api\Error\Oxygen;
use Undine\Api\Error\Response;
use Undine\Api\Error\SiteConnect;
use Undine\Api\Exception\ConstraintViolationException;
use Undine\Api\Result\SiteConnectResult;
use Undine\Api\Result\SiteLoginResult;
use Undine\Api\Result\SiteLogoutResult;
use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\Api;
use Undine\Drupal\Data\ModuleList;
use Undine\Drupal\Exception as DCE;
use Undine\Drupal\Session;
use Undine\Event\Events;
use Undine\Event\SiteConnectEvent;
use Undine\Event\SiteDisconnectEvent;
use Undine\Model\Site;
use Undine\Oxygen\Action\ModuleDisableAction;
use Undine\Oxygen\Action\SiteLogoutAction;
use Undine\Oxygen\Action\SitePingAction;
use Undine\Oxygen\Exception\OxygenErrorCode;
use Undine\Oxygen\Exception\ResponseException;
use Undine\Oxygen\Reaction\SiteLogoutReaction;
use Undine\Oxygen\Reaction\SitePingReaction;

class SiteController extends AppController
{
    /**
     * @Method("GET|POST")
     * @Route("site.connect", name="api-site.connect")
     * @Api("Undine\Form\Type\Api\SiteConnectType", streamable=true)
     */
    public function connectAction(SiteConnectCommand $command, callable $stream)
    {
        list($privateKey, $publicKey) = \Undine\Functions\openssl_generate_rsa_key_pair();
        $site = (new Site($command->getUrl(), $this->getUser(), $privateKey, $publicKey))
            ->setHttpCredentials($command->getHttpCredentials())
            ->setFtpCredentials($command->getFtpCredentials());

        $drupalClient  = $this->get('undine.drupal_client');
        $drupalSession = new Session(new CookieJar(), $command->getHttpCredentials(), $command->getFtpCredentials());
        // This promise can be present or not, we create a reference here so we can cancel it if it proves to be unnecessary (ie. we're already connected).
        $findLoginForm = null;

        // First asynchronously attempt a handshake...
        $connectWebsite = $this->oxygenClient->sendAsync($site, new SitePingAction())
            ->then(function ($result) use (&$findLoginForm) {
                // We got a successful handshake; cancel the form lookup.
                if ($findLoginForm instanceof Promise && $findLoginForm->getState() === Promise::PENDING) {
                    $findLoginForm->cancel();
                }

                return $result;
            });
        // ...and at the same time check if the login form could be found, because most likely the site won't be connected immediately.
        if ($command->checkUrl() || $command->hasAdminCredentials()) {
            $findLoginForm = $drupalClient->findLoginFormAsync($command->getUrl(), $drupalSession);
        }

        /** @var Promise $settlePromise */
        $settlePromise = \GuzzleHttp\Promise\settle(array_filter([$connectWebsite, $findLoginForm]));
        // [0] will contain 'connectWebsite' result; while [1] will contain 'findLoginForm' result or will not be set.
        // Also, result arrays contain two members: 'state' and 'value' (if fulfilled) or 'reason' (if rejected).
        $result = $settlePromise->wait();

        if ($result[0]['state'] === Promise::FULFILLED) {
            // Site connection was fully successful.
            $this->persistSite($site);

            return new SiteConnectResult($site);
        }

        // If we got here, we didn't get a successful handshake.

        if ($result[0]['reason'] instanceof \Exception) {
            throw $result[0]['reason'];
        }

        // Oxygen client always resolves
        $constraint = $result[0]['reason'];

        if ($constraint instanceof Response\OxygenNotFound && $findLoginForm) {
            // The Oxygen module is not enabled and we did look for a login form.
            if ($result[1]['state'] === Promise::FULFILLED) {
                // We got a login form!
                /** @var Form $form */
                $form = $result[1]['value'];
                if (!$command->hasAdminCredentials()) {
                    throw new ConstraintViolationException(new SiteConnect\OxygenNotFound(true, true));
                }
                // We got admin credentials provided; log in and install the Oxygen module.
                try {
                    $drupalClient->login($form, $command->getAdminCredentials()->getUsername(), $command->getAdminCredentials()->getPassword(), $drupalSession);
                } catch (DCE\InvalidCredentialsException $e) {
                    throw new ConstraintViolationException(new DrupalClient\InvalidCredentials());
                }
                try {
                    $modulesForm = $drupalClient->getModulesForm($command->getUrl(), $drupalSession);
                } catch (DCE\ModulesFormNotFoundException $e) {
                    throw new ConstraintViolationException(new DrupalClient\CanNotInstallOxygen(DrupalClient\CanNotInstallOxygen::STEP_LIST_MODULES));
                }
                $moduleList   = ModuleList::createFromForm($modulesForm);
                $updateModule = $moduleList->find('update', 'Core');
                if ($updateModule === null) {
                    throw new ConstraintViolationException(new DrupalClient\CanNotInstallOxygen(DrupalClient\CanNotInstallOxygen::STEP_SEARCH_UPDATE_MODULE));
                }
                if (!$updateModule->isEnabled()) {
                    $drupalClient->enableModule($modulesForm, $updateModule->getPackage(), $updateModule->getSlug(), $drupalSession);
                }
                $oxygenModule = $moduleList->find('oxygen');
                if ($oxygenModule === null) {
                    // Oxygen module is not installed; install it now.
                    try {
                        $drupalClient->installExtensionFromUrl($command->getUrl(), $this->getParameter('oxygen_zip_url'), $drupalSession);
                    } catch (DCE\FtpCredentialsRequiredException $e) {
                        throw new ConstraintViolationException(new Ftp\CredentialsRequired());
                    } catch (DCE\FtpCredentialsErrorException $e) {
                        throw new ConstraintViolationException(new Ftp\CredentialsError($e->getClientMessage()));
                    }
                    $modulesForm   = $drupalClient->getModulesForm($command->getUrl(), $drupalSession);
                    $newModuleList = ModuleList::createFromForm($modulesForm);
                    $oxygenModule  = $newModuleList->find('oxygen');
                    if ($oxygenModule === null) {
                        throw new ConstraintViolationException(new DrupalClient\CanNotInstallOxygen(DrupalClient\CanNotInstallOxygen::STEP_SEARCH_OXYGEN_MODULE));
                    }
                }
                if (!$oxygenModule->isEnabled()) {
                    $drupalClient->enableModule($modulesForm, $oxygenModule->getPackage(), $oxygenModule->getSlug(), $drupalSession);
                }
                try {
                    // The module might have already been connected to an account - clear its key.
                    $drupalClient->disconnectOxygen($command->getUrl(), $drupalSession);
                } catch (DCE\OxygenPageNotFoundException $e) {
                    throw new ConstraintViolationException(new DrupalClient\OxygenPageNotFound());
                }
                // @todo: Make sure the module is at the latest version.
                try {
                $this->oxygenClient->send($site, new SitePingAction());
                } catch (RejectionException $e) {
//                    $e->get
                }
                // Site connection was fully successful.
                $this->persistSite($site);

                return new SiteConnectResult($site);
            } elseif ($result[1]['reason'] instanceof DCE\LoginFormNotFoundException) {
                // No login form found, is this even a Drupal website?
                throw new ConstraintViolationException(new SiteConnect\OxygenNotFound(true, false));
            }
        } elseif ($constraint instanceof Oxygen\ExceptionConstraint && $constraint->getCode() === OxygenErrorCode::HANDSHAKE_VERIFY_FAILED) {
            // The site is connected to another dashboard, but we can reclaim it here if the admin credentials are provided.
            /** @var ResponseException $oxygenException */
            if ($findLoginForm === null) {
                // We did not look for a login form.
                throw new ConstraintViolationException(new SiteConnect\OxygenAlreadyConnected(false, false));
            }
            // We looked for the login form.
            if ($result[1]['state'] === Promise::FULFILLED) {
                // We have a login form.
                /** @var Form $form */
                $loginForm = $result[1]['value'];
                if (!$command->hasAdminCredentials()) {
                    throw new ConstraintViolationException(new SiteConnect\OxygenAlreadyConnected(true, true));
                }
                try {
                    $drupalClient->login($loginForm, $command->getAdminCredentials()->getUsername(), $command->getAdminCredentials()->getPassword(), $drupalSession);
                } catch (DCE\InvalidCredentialsException $e) {
                    throw new ConstraintViolationException(new DrupalClient\InvalidCredentials());
                }
                try {
                    $drupalClient->disconnectOxygen($command->getUrl(), $drupalSession);
                } catch (DCE\OxygenPageNotFoundException $e) {
                    throw new ConstraintViolationException(new DrupalClient\OxygenPageNotFound());
                }
                // @todo: Make sure the module is at the latest version.
                $this->oxygenClient->send($site, new SitePingAction());
                // Site connection was fully successful.
                $this->persistSite($site);

                return new SiteConnectResult($site);
            }
            // We did not find a login form.
            throw new ConstraintViolationException(new SiteConnect\OxygenAlreadyConnected(true, false));
        }

        throw new ConstraintViolationException($result[0]['reason']);
    }

    private function persistSite(Site $site)
    {
        $event = new SiteConnectEvent($site);
        $this->dispatcher->dispatch(Events::SITE_CONNECT, $event);

        $this->em->persist($site);
        $this->em->persist($site->getSiteState());
        array_map([$this->em, 'persist'], $site->getSiteState()->getSiteExtensions());
        array_map([$this->em, 'persist'], $site->getSiteState()->getSiteUpdates());

        $this->em->flush();
    }

    /**
     * @Route("site.ping", name="api-site.ping")
     * @Api("Undine\Form\Type\Api\SitePingType", bulkable=true)
     */
    public function pingAction(SitePingCommand $command)
    {
        return $this->oxygenClient->sendAsync($command->getSite(), new SitePingAction())
            ->then(function (SitePingReaction $reaction) use ($command) {
                return new SiteConnectResult($command->getSite());
            });
    }

    /**
     * @Method("GET|POST")
     * @Route("site.disconnect", name="api-site.disconnect")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site"})
     * @Api()
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
     * @Method("GET|POST")
     * @Route("site.login", name="api-site.login")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site"})
     * @Api()
     */
    public function loginAction(Site $site)
    {
        $loginUrl = $this->oxygenLoginUrlGenerator->generateUrl($site, $this->getUser()->getId());

        return new SiteLoginResult($site, $loginUrl);
    }

    /**
     * @Route("site.logout", name="api-site.logout")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site"})
     * @Api()
     */
    public function logoutAction(Site $site)
    {
        /** @var SiteLogoutReaction $reaction */
        $reaction = $this->oxygenClient->send($site, new SiteLogoutAction($this->getUser()->getId()));

        return new SiteLogoutResult($site, $reaction->getDestroyedSessions());
    }
}
