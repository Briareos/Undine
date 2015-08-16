<?php

namespace Undine\AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Undine\AppBundle\Controller\AppController;

class DefaultController extends AppController
{
    /**
     * @Route("/", name="admin-home")
     * @Template("admin/default/home.html.twig")
     */
    public function homeAction()
    {
        return [];
    }
}
