<?php

namespace Undine\AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Undine\Api\Command\ProjectInstallFromUrlCommand;
use Undine\Api\Result\ProjectInstallFromUrlResult;
use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\ApiCommand;
use Undine\Configuration\ApiResult;
use Undine\Model\Site;
use Undine\Oxygen\Action\ProjectInstallFromUrlAction;
use Undine\Oxygen\Reaction\ProjectInstallFromUrlReaction;

class ProjectController extends AppController
{
    /**
     * @Route("project.installFromUrl", name="api-project.installFromUrl")
     * @ApiCommand("api__project_install_from_url")
     * @ParamConverter("site", class="Model:Site", options={"request_path":"site", "query_path":"site", "repository_method":"findOneByUid"})
     * @ApiResult()
     */
    public function installFromUrlAction(Site $site, ProjectInstallFromUrlCommand $command)
    {
        /** @var ProjectInstallFromUrlReaction $reaction */
        $reaction = $this->oxygenClient->send($site, new ProjectInstallFromUrlAction($command->getUrl()));

        return new ProjectInstallFromUrlResult();
    }
}
