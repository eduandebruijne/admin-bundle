<?php

declare(strict_types=1);

namespace EDB\AdminBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class LoginFailedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [LoginFailureEvent::class => 'addDangerFlash'];
    }

    public function addDangerFlash()
    {
        $request = $this->requestStack->getCurrentRequest();
        $request->getSession()->getFlashBag()->add('danger', 'Invalid credentials');
    }
}
