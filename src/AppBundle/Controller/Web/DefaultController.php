<?php

namespace Undine\AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="web-homepage")
     * @Template("default/index.html.twig")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return [
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ];
    }
}
