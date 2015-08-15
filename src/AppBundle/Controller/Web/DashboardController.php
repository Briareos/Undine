<?php

namespace Undine\AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Undine\AppBundle\Controller\AppController;

class DashboardController extends AppController
{
    /**
     * @Method("GET")
     * @Route("/dashboard/{page}", name="web-dashboard", defaults={"page"=""}, requirements={"page"=".*"})
     */
    public function appAction()
    {
        return $this->render(sprintf('dashboard/%s.html.twig', $this->container->getParameter('kernel.environment')));
    }
}
