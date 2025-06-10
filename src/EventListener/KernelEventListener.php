<?php

namespace App\EventListener;

use App\Utils\LocaleUtil;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use TripleE\Utilities\ParameterBagUtil;

class KernelEventListener implements EventSubscriberInterface
{
    public function __construct(
        ParameterBagInterface $parameterBag
    ) {
        ParameterBagUtil::$bag = $parameterBag;
    }
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
            KernelEvents::RESPONSE => ['onKernelResponse', 9999],
            KernelEvents::EXCEPTION => ['onKernelException', 9999]
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->addCorsHeaders($event);
    }

    public function addCorsHeaders(KernelEvent $event): void
    {
        $response = $event->getResponse();
        if ($response) {
            $allowOrigin = $this->getAllowOrigin($event->getRequest());
            if ($allowOrigin) {
                $response->headers->set('Access-Control-Allow-Origin', $allowOrigin);
                $response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH');
                $response->headers->set('Access-Control-Allow-Headers', 'Origin, content-type, accept');
                $response->headers->set('Access-Control-Allow-Credentials', 'true');
            }
        }
    }

    private function getAllowOrigin(Request $request): ?string
    {
        if(!$request->headers->has('Origin')) return null;
        if(preg_match(ParameterBagUtil::$bag->get('cors.origin'), $request->headers->get('Origin'))) {
            return $request->headers->get('Origin');
        }
        return null;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Don't do anything if it's not the master request.
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $method = $request->getRealMethod();

        if (Request::METHOD_OPTIONS === $method) {
            $response = new Response();
            $event->setResponse($response);
        }

        LocaleUtil::$locale = $request->getLocale();
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        // Don't do anything if it's not the master request.
        if (!$event->isMainRequest()) {
            return;
        }

        $this->addCorsHeaders($event);
    }
}