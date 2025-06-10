<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginEventListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $loginLogger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => "onLoginSuccess",
            LoginFailureEvent::class => "onLoginFailure"
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $this->loginLogger->info(
            $event->getFirewallName() . " | " .
            $event->getUser()->getId() . " | " .
            $event->getUser()->__tostring() . " | " .
            $event->getRequest()->getClientIp()
        );
    }

    public function onLoginFailure(LoginFailureEvent $event)
    {
        if (is_null($event->getPassport())) {
            $this->loginLogger->warning(
                $event->getFirewallName() . " | " .
                $event->getException()?->getMessage() . " | " .
                $event->getRequest()->getClientIp()
            );
        } else {
            $this->loginLogger->warning(
                sprintf(
                    "%s | %s | %s | %s",
                    $event->getFirewallName(),
                    $event->getPassport()->getUser()?->__toString(),
                    $event->getException()?->getMessage(),
                    $event->getRequest()->getClientIp(),
                )
            );
        }
    }
}