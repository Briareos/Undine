<?php

namespace Undine\AppBundle\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Undine\Api\Result\AuthTestResult;
use Undine\AppBundle\Controller\AppController;
use Undine\Configuration\Api;

class AuthController extends AppController
{
    /**
     * @Route("auth.test", name="api-auth.test")
     * @Api()
     */
    public function testAction()
    {
        return new AuthTestResult($this->getUser());
    }
}
