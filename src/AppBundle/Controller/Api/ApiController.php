<?php

namespace Undine\AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Undine\Api\Result\ApiTestResult;
use Undine\Api\Result\ApiMeResult;
use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\Api;

class ApiController extends AppController
{
    /**
     * @Method("GET|POST")
     * @Route("api.test", name="api-api.test")
     * @Api()
     */
    public function testAction()
    {
        return new ApiTestResult();
    }

    /**
     * @Method("GET|POST")
     * @Route("api.me", name="api-api.me")
     * @Api()
     */
    public function meAction()
    {
        return new ApiMeResult($this->getUser());
    }
}
