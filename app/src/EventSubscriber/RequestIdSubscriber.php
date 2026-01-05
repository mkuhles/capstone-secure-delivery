<?php

namespace App\EventSubscriber;

use App\Security\RequestId;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class RequestIdSubscriber implements EventSubscriberInterface
{
    public const ATTR = 'request_id';

    public function __construct(
        private RequestId $requestId,
        /** @var string[] */
        private array $trustedProxies = [],
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['onKernelRequest', 1000],
            ResponseEvent::class => ['onKernelResponse', -1000],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $remoteAddr = (string) $request->server->get('REMOTE_ADDR', '');
        $trusted = in_array($remoteAddr, $this->trustedProxies, true);

        $incoming =
            $request->headers->get(RequestId::HEADER_PRIMARY)
            ?? $request->headers->get(RequestId::HEADER_FALLBACK);

        $rid = $this->requestId->resolve(is_string($incoming) ? $incoming : null, $trusted);

        $request->attributes->set(self::ATTR, $rid);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $rid = (string) $request->attributes->get(self::ATTR, '');

        if ($rid !== '') {
            $event->getResponse()->headers->set(RequestId::HEADER_PRIMARY, $rid);
        }
    }
}