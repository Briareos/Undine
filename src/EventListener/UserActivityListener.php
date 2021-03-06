<?php

namespace Undine\EventListener;

use Undine\Security\User\UserActivityAwareInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserActivityListener implements EventSubscriberInterface
{
    private $tokenStorage;

    private $em;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManager $em)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -10],
            SecurityEvents::INTERACTIVE_LOGIN => ['onSecurityInteractiveLogin', -10],
        ];
    }

    public function onKernelRequest(GetResponseEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $dispatcher->addListener(KernelEvents::TERMINATE, function () {
            $token = $this->tokenStorage->getToken();
            if (!$token || !($user = $token->getUser()) instanceof UserActivityAwareInterface) {
                return;
            }
            /* @var UserActivityAwareInterface $user */
            $uow = $this->em->getUnitOfWork();
            if (!$uow->isScheduledForInsert($user) && !$uow->isInIdentityMap($user)) {
                return;
            }
            if ($user->getLastActiveAt() && ((new \DateTime())->getTimestamp() - $user->getLastActiveAt()->getTimestamp()) < 60) {
                return;
            }
            $user->setLastActiveAt(new \DateTime());
            $this->em->persist($user);
            $this->em->flush($user);
        }, -10);
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $token = $event->getAuthenticationToken();

        $dispatcher->addListener(KernelEvents::TERMINATE, function () use ($token) {
            if (!($user = $token->getUser()) instanceof UserActivityAwareInterface) {
                return;
            }
            /* @var UserActivityAwareInterface $user */
            $uow = $this->em->getUnitOfWork();
            if (!$uow->isScheduledForInsert($user) && !$uow->isInIdentityMap($user)) {
                return;
            }
            $user->setLastLoginAt(new \DateTime());
            $this->em->persist($user);
            $this->em->flush($user);
        }, -10);
    }
}
