<?php

namespace Undine\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonBodyListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->isMethodSafe() || $request->request->count()) {
            return;
        }

        if ($request->getContentType() !== 'json') {
            return;
        }

        $body = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException(sprintf('The JSON body provided could not be parsed: %s', json_last_error_msg()));
        }

        if (!is_array($body)) {
            throw new BadRequestHttpException(sprintf('The JSON body must be an object; %s given.', gettype($body)));
        }

        $request->request->replace($body);
    }
}