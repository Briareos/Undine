<?php

namespace Undine\AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Undine\AppBundle\Controller\AppController;

class DefaultController extends AppController
{
    /**
     * @Route("/", name="web-home")
     * @Template("web/default/home.html.twig")
     */
    public function indexAction()
    {
        return [];
    }
}
