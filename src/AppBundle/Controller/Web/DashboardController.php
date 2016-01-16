<?php

namespace Undine\AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Undine\AppBundle\Controller\AppController;

class DashboardController extends AppController
{
    /**
     * @Method("GET")
     * @Route("/dashboard/{page}", name="web-dashboard", defaults={"page"=""}, requirements={"page"=".*"})
     * @Template("dashboard/app.html.twig")
     */
    public function appAction()
    {
        return [];
    }
}
