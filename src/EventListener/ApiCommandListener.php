<?php

namespace Undine\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Undine\Api\Exception\CommandInvalidException;
use Undine\Configuration\ApiCommand;

class ApiCommandListener implements EventSubscriberInterface
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', -255],
        ];
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('_api_command')) {
            return;
        }

        /** @var ApiCommand $apiCommand */
        $apiCommand = $request->attributes->get('_api_command');

        $type = $apiCommand->getType();

        $formOptions = [
            'csrf_protection'    => false,
            'validation_groups'  => $apiCommand->getGroups(),
            // "Loose" form validation, allow extra fields.
            // @todo: Change this to only accept "token" and a few generic others if needed.
            'allow_extra_fields' => true,
        ];

        if (!empty($apiCommand->getGroups())) {
            $formOptions['validation_groups'] = $apiCommand->getGroups();
        }

        $formData = null;
        if ($request->attributes->has($apiCommand->getName())) {
            $formData = $request->attributes->get($apiCommand->getName());
        }

        $form = $this->formFactory->createNamed('', $type, $formData, $formOptions);

        // "Loose" form submission, POST => GET => FILES.
        // @todo: Throw exception on parameter collision.
        $form->submit(array_replace_recursive($request->request->all(), $request->query->all(), $request->files->all()), !$request->isMethod('PATCH'));

        if (!$form->isValid()) {
            throw new CommandInvalidException($form);
        }

        $request->attributes->set($apiCommand->getName(), $form->getData());
    }
}
