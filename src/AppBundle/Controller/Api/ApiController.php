<?php

namespace Undine\AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Undine\Api\Result\ApiTestResult;
use Undine\Api\Result\ApiMeResult;
use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\ApiResult;

class ApiController extends AppController
{
    /**
     * @Route("api.test", name="api-api.test")
     * @ApiResult()
     */
    public function testAction()
    {
        return new ApiTestResult();
    }

    /**
     * @Route("api.me", name="api-api.me")
     * @ApiResult()
     */
    public function meAction()
    {
        return new ApiMeResult($this->getUser());
    }
}
