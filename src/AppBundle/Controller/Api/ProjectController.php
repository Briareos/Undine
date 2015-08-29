<?php

namespace Undine\AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\ApiCommand;
use Undine\Configuration\ApiResult;

class ProjectController extends AppController
{
    /**
     * @Route("project.install", name="api-project.install")
     * @ApiCommand("api__project_install")
     * @ApiResult()
     */
    public function enableAction()
    {

    }
}
