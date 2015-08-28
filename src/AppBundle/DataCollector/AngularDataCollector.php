<?php

namespace Undine\AppBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * An empty collector class. It is required for creating a web debug toolbar section.
 */
class AngularDataCollector extends DataCollector
{
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = [];
    }

    public function getName()
    {
        return 'angular';
    }
}