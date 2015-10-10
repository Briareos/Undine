<?php

namespace Undine\AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Undine\Api\Command\ModuleDisableCommand;
use Undine\Api\Command\ModuleEnableCommand;
use Undine\Api\Result\ModuleDisableResult;
use Undine\Api\Result\ModuleEnableResult;
use Undine\Api\Result\ModuleUninstallResult;
use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\Api;
use Undine\Model\Site;
use Undine\Oxygen\Action\ModuleDisableAction;
use Undine\Oxygen\Action\ModuleEnableAction;
use Undine\Oxygen\Action\ModuleUninstallAction;
use Undine\Oxygen\Reaction\ModuleDisableReaction;
use Undine\Oxygen\Reaction\ModuleEnableReaction;
use Undine\Oxygen\Reaction\ModuleUninstallReaction;

class ModuleController extends AppController
{
    /**
     * @Method("GET|POST")
     * @Route("module.enable", name="api-module.enable")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
     * @Api("api__module_enable")
     */
    public function enableAction(Site $site, ModuleEnableCommand $command)
    {
        /** @var ModuleEnableReaction $reaction */
        $reaction = $this->oxygenClient->send($site, new ModuleEnableAction([$command->getModule()], $command->enableDependencies()));

        return new ModuleEnableResult();
    }

    /**
     * @Method("GET|POST")
     * @Route("module.disable", name="api-module.disable")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
     * @Api("api__module_disable")
     */
    public function disableAction(Site $site, ModuleDisableCommand $command)
    {
        /** @var ModuleDisableReaction $reaction */
        $reaction = $this->oxygenClient->send($site, new ModuleDisableAction([$command->getModule()], $command->disableDependents()));

        return new ModuleDisableResult();
    }

    /**
     * @Method("GET|POST")
     * @Route("module.uninstall", name="api-module.uninstall")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
     * @Api("api__module_disable")
     */
    public function uninstallAction(Site $site, ModuleDisableCommand $command)
    {
        /** @var ModuleUninstallReaction $reaction */
        $reaction = $this->oxygenClient->send($site, new ModuleUninstallAction([$command->getModule()], $command->disableDependents()));

        return new ModuleUninstallResult();
    }
}
