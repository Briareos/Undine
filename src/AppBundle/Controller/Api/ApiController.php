<?php

namespace Undine\AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Undine\Api\Result\EmptyResult;
use Undine\Configuration\ApiResult;

class ApiController
{
    /**
     * @Route("api.test", name="api-api.test")
     * @ApiResult()
     */
    public function testAction()
    {
        return new EmptyResult();
    }
}
