<?php

namespace Undine\AppBundle\Controller\Api;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Promise;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DomCrawler\Form;
use Undine\Api\Command\SiteConnectCommand;
use Undine\Api\Command\SitePingCommand;
use Undine\Api\Constraint\Site\CanNotInstallOxygenConstraint;
use Undine\Api\Constraint\Site\CanNotResolveHostConstraint;
use Undine\Api\Constraint\Site\FtpCredentialsErrorConstraint;
use Undine\Api\Constraint\Site\FtpCredentialsRequiredConstraint;
use Undine\Api\Constraint\Site\HttpAuthenticationRequiredConstraint;
use Undine\Api\Constraint\Site\HttpAuthenticationFailedConstraint;
use Undine\Api\Constraint\Site\InvalidCredentialsConstraint;
use Undine\Api\Constraint\Site\InvalidHttpStatusCodeConstraint;
use Undine\Api\Constraint\Site\NoResponseConstraint;
use Undine\Api\Constraint\Site\OxygenAlreadyConnectedConstraint;
use Undine\Api\Constraint\Site\OxygenNotEnabledConstraint;
use Undine\Api\Constraint\Site\OxygenPageNotFoundConstraint;
use Undine\Api\Constraint\Site\OxygenErrorConstraint;
use Undine\Api\Exception\ConstraintViolationException;
use Undine\Api\Exception\RejectedPromiseException;
use Undine\Api\Result\SiteConnectResult;
use Undine\Api\Result\SiteLoginResult;
use Undine\Api\Result\SiteLogoutResult;
use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\Api;
use Undine\Drupal\Data\ModuleList;
use Undine\Drupal\Exception\FtpCredentialsErrorException;
use Undine\Drupal\Exception\FtpCredentialsRequiredException;
use Undine\Drupal\Exception\InvalidCredentialsException;
use Undine\Drupal\Exception\LoginFormNotFoundException;
use Undine\Drupal\Exception\ModulesFormNotFoundException;
use Undine\Drupal\Exception\OxygenPageNotFoundException;
use Undine\Drupal\Session;
use Undine\Event\Events;
use Undine\Event\SiteDisconnectEvent;
use Undine\Model\Site;
use Undine\Oxygen\Action\ModuleDisableAction;
use Undine\Oxygen\Action\SiteLogoutAction;
use Undine\Oxygen\Action\SitePingAction;
use Undine\Oxygen\Exception\InvalidBodyException;
use Undine\Oxygen\Exception\OxygenException;
use Undine\Oxygen\Reaction\SiteLogoutReaction;
use Undine\Oxygen\Reaction\SitePingReaction;

class SiteController extends AppController
{
    /**
     * @Method("GET|POST")
     * @Route("site.connect", name="api-site.connect")
     * @Api("api__site_connect", streamable=true)
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

        $connectWebsite = $this->oxygenClient->sendAsync($site, new SitePingAction())
            ->then(function ($result) use (&$findLoginForm) {
                // We got a successful handshake; cancel the form lookup.
                if ($findLoginForm instanceof Promise && $findLoginForm->getState() === Promise::PENDING) {
                    $findLoginForm->cancel();
                }

                return $result;
            });

        if ($command->checkUrl() || $command->hasAdminCredentials()) {
            $findLoginForm = $drupalClient->findLoginFormAsync($command->getUrl(), $drupalSession);
        }
        /** @var Promise $settlePromise */
        $settlePromise = \GuzzleHttp\Promise\settle(array_filter([$connectWebsite, $findLoginForm]));
        // [0] will contain 'connectWebsite' result; while [1] will contain 'findLoginForm' result or will not be set.
        // Also, result arrays contain two members: 'state' and 'value' (if fulfilled) or 'reason' (if rejected).
        $result = $settlePromise->wait();

        if ($result[0]['state'] == Promise::FULFILLED) {
            // ISite connection was fully successful.
            $this->persistSite($site);

            return new SiteConnectResult($site);
        } elseif ($result[0]['reason'] instanceof InvalidBodyException) {
            /** @var InvalidBodyException $invalidBodyException */
            $invalidBodyException = $result[0]['reason'];
            if ($invalidBodyException->getResponse()->hasHeader('www-authenticate')) {
                // We got HTTP's "authorization required" page.
                if ($command->hasHttpCredentials()) {
                    throw new ConstraintViolationException(new HttpAuthenticationFailedConstraint());
                }
                throw new ConstraintViolationException(new HttpAuthenticationRequiredConstraint());
            }

            // The Oxygen module is not enabled.
            if ($findLoginForm !== null) {
                // We did look for a login form.
                if ($result[1]['state'] === Promise::FULFILLED) {
                    // We got a login form!
                    /** @var Form $form */
                    $form = $result[1]['value'];
                    if (!$command->hasAdminCredentials()) {
                        throw new ConstraintViolationException(new OxygenNotEnabledConstraint(true, true));
                    }
                    // We got admin credentials provided; log in and install the Oxygen module.
                    try {
                        $drupalClient->login($form, $command->getAdminCredentials()->getUsername(), $command->getAdminCredentials()->getPassword(), $drupalSession);
                    } catch (InvalidCredentialsException $e) {
                        throw new ConstraintViolationException(new InvalidCredentialsConstraint());
                    }
                    try {
                        $modulesForm = $drupalClient->getModulesForm($command->getUrl(), $drupalSession);
                    } catch (ModulesFormNotFoundException $e) {
                        throw new ConstraintViolationException(new CanNotInstallOxygenConstraint(CanNotInstallOxygenConstraint::STEP_LIST_MODULES));
                    }
                    $moduleList   = ModuleList::createFromForm($modulesForm);
                    $updateModule = $moduleList->find('update', 'Core');
                    if ($updateModule === null) {
                        throw new ConstraintViolationException(new CanNotInstallOxygenConstraint(CanNotInstallOxygenConstraint::STEP_SEARCH_UPDATE_MODULE));
                    }
                    if (!$updateModule->isEnabled()) {
                        $drupalClient->enableModule($modulesForm, $updateModule->getPackage(), $updateModule->getSlug(), $drupalSession);
                    }
                    $oxygenModule = $moduleList->find('oxygen');
                    if ($oxygenModule === null) {
                        // Oxygen module is not installed; install it now.
                        try {
                            $drupalClient->installExtensionFromUrl($command->getUrl(), $this->getParameter('oxygen_zip_url'), $drupalSession);
                        } catch (FtpCredentialsRequiredException $e) {
                            throw new ConstraintViolationException(new FtpCredentialsRequiredConstraint());
                        } catch (FtpCredentialsErrorException $e) {
                            throw new ConstraintViolationException(new FtpCredentialsErrorConstraint($e->getClientMessage()));
                        }
                        $modulesForm   = $drupalClient->getModulesForm($command->getUrl(), $drupalSession);
                        $newModuleList = ModuleList::createFromForm($modulesForm);
                        $oxygenModule  = $newModuleList->find('oxygen');
                        if ($oxygenModule === null) {
                            throw new ConstraintViolationException(new CanNotInstallOxygenConstraint(CanNotInstallOxygenConstraint::STEP_SEARCH_OXYGEN_MODULE));
                        }
                    }
                    if (!$oxygenModule->isEnabled()) {
                        $drupalClient->enableModule($modulesForm, $oxygenModule->getPackage(), $oxygenModule->getSlug(), $drupalSession);
                    }
                    try {
                        // The module might have already been connected to an account - clear its key.
                        $drupalClient->disconnectOxygen($command->getUrl(), $drupalSession);
                    } catch (OxygenPageNotFoundException $e) {
                        throw new ConstraintViolationException(new OxygenPageNotFoundConstraint());
                    }
                    // @todo: Make sure the module is at the latest version.
                    $this->oxygenClient->send($site, new SitePingAction());
                    // ISite connection was fully successful.
                    $this->persistSite($site);

                    return new SiteConnectResult($site);
                } elseif ($result[1]['reason'] instanceof LoginFormNotFoundException) {
                    // No login form found, is this even a Drupal website?
                    throw new ConstraintViolationException(new OxygenNotEnabledConstraint(true, false));
                }
            }
            throw new ConstraintViolationException(new OxygenNotEnabledConstraint(false, false));
        } elseif ($result[0]['reason'] instanceof OxygenException) {
            // We got an exception from the module itself.
            /** @var OxygenException $oxygenException */
            $oxygenException = $result[0]['reason'];
            if ($oxygenException->getCode() === OxygenException::HANDSHAKE_VERIFY_FAILED) {
                // Treat this exception as "special" in this specific API call, since we can recover from it here.
                if ($findLoginForm !== null) {
                    // We looked for the login form.
                    if ($result[1]['state'] === Promise::FULFILLED) {
                        // We have a login form.
                        /** @var Form $form */
                        $loginForm = $result[1]['value'];
                        if (!$command->hasAdminCredentials()) {
                            throw new ConstraintViolationException(new OxygenAlreadyConnectedConstraint(true, true));
                        }
                        try {
                            $drupalClient->login($loginForm, $command->getAdminCredentials()->getUsername(), $command->getAdminCredentials()->getPassword(), $drupalSession);
                        } catch (InvalidCredentialsException $e) {
                            throw new ConstraintViolationException(new InvalidCredentialsConstraint());
                        }
                        try {
                            $drupalClient->disconnectOxygen($command->getUrl(), $drupalSession);
                        } catch (OxygenPageNotFoundException $e) {
                            throw new ConstraintViolationException(new OxygenPageNotFoundConstraint());
                        }
                        // @todo: Make sure the module is at the latest version.
                        $this->oxygenClient->send($site, new SitePingAction());
                        // ISite connection was fully successful.
                        $this->persistSite($site);

                        return new SiteConnectResult($site);
                    }
                    // We did not find a login form.
                    throw new ConstraintViolationException(new OxygenAlreadyConnectedConstraint(true, false));
                }
                // We did not look for a login form.
                throw new ConstraintViolationException(new OxygenAlreadyConnectedConstraint(false, false));
            }
            throw new ConstraintViolationException(new OxygenErrorConstraint($oxygenException->getCode(), $oxygenException->getType()));
        } elseif ($result[0]['reason'] instanceof RequestException) {
            // A lower-level request exception occurred.
            // @todo: Move this to a generic site connection exception handler.
            /** @var RequestException $connectException */
            $connectException = $result[0]['reason'];
            $context          = $connectException->getHandlerContext();
            $context += [
                'errno' => 0,
                'error' => '',
            ];
            if (!$connectException->hasResponse()) {
                // No response means there might be an HTTP protocol error.
                switch ($context['errno']) {
                    case CURLE_COULDNT_RESOLVE_HOST:
                        $violation = new CanNotResolveHostConstraint();
                        break;
                    default:
                        $violation = new NoResponseConstraint($context['errno'], $context['error']);
                }
                throw new ConstraintViolationException($violation);
            } else {
                // We got an invalid (or rather unhandled) HTTP status code.
                throw new ConstraintViolationException(new InvalidHttpStatusCodeConstraint($connectException->getCode()));
            }
        } else {
            // This is an application-level exception; users should never see them, so don't attempt to silence them here.
            throw new \RuntimeException('A promise was rejected with an unexpected exception.', 0, RejectedPromiseException::wrap($result[0]['reason']));
        }
    }

    private function persistSite(Site $site)
    {
        $extensions = $site->getSiteExtensions();
        $updates    = $site->getSiteUpdates();

        // Since part of their primary key is site's ID, we can't cascade-persist them,
        // so detach them and persist them after a site ID has been assigned.
        $site->setSiteExtensions([]);
        $site->setSiteUpdates([]);

        $this->em->persist($site);
        $this->em->flush($site);

        $site->setSiteExtensions($extensions);
        $site->setSiteUpdates($updates);

        array_map([$this->em, 'persist'], $site->getSiteExtensions());
        array_map([$this->em, 'persist'], $site->getSiteUpdates());

        $this->em->flush();
    }

    /**
     * @Route("site.ping", name="api-site.ping")
     * @Api("api__site_ping", bulkable=true)
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
     * @ParamConverter("site", class="Model:ISite", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
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
     * @ParamConverter("site", class="Model:ISite", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
     * @Api()
     */
    public function loginAction(Site $site)
    {
        $loginUrl = $this->oxygenLoginUrlGenerator->generateUrl($site, $this->getUser()->getUid());

        return new SiteLoginResult($site, $loginUrl);
    }

    /**
     * @Route("site.logout", name="api-site.logout")
     * @ParamConverter("site", class="Model:ISite", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
     * @Api()
     */
    public function logoutAction(Site $site)
    {
        /** @var SiteLogoutReaction $reaction */
        $reaction = $this->oxygenClient->send($site, new SiteLogoutAction($this->getUser()->getUid()));

        return new SiteLogoutResult($site, $reaction->getDestroyedSessions());
    }
}
