<?php

namespace Undine\EventListener\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter as BaseDoctrineParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Adds support for 'request_path' and 'query_path', to pull values from POST and GET respectively.
 */
class DoctrineParamConverter extends BaseDoctrineParamConverter
{
    public function apply(Request $request, ParamConverter $configuration)
    {
        if ($request->isMethod('POST') && !empty($configuration->getOptions()['request_path'])) {
            $requestPath = $configuration->getOptions()['request_path'];
            $request->attributes->set($requestPath, $request->request->get($requestPath));
        } elseif ($request->isMethod('GET') && !empty($configuration->getOptions()['query_path'])) {
            $queryPath = $configuration->getOptions()['query_path'];
            $request->attributes->set($queryPath, $request->query->get($queryPath));
        }

        return parent::apply($request, $configuration);
    }
}
