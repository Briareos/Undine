<?php

namespace Undine\AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Undine\AppBundle\Controller\AppController;

/**
 * @Route("/docs")
 */
class DocsController extends AppController
{
    /**
     * @Route("/api", name="web-docs_api")
     * @Template("web/docs/api.html.twig")
     */
    public function apiAction()
    {
        return [];
    }
}
