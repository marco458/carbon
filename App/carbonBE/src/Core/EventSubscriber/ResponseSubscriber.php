<?php

declare(strict_types=1);

namespace Core\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseSubscriber implements EventSubscriberInterface
{
    private const SENTRY_TRACE_HEADER = 'Sentry-Trace';
    private const BAGGAGE_HEADER = 'Baggage';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [ResponseEvent::class => 'onKernelResponse'];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {
            return;
        }

        // passthrough Sentry-Trace headers, required for Angular/React Sentry implementation
        $sentryTrace = $request->headers->get(self::SENTRY_TRACE_HEADER);
        $baggage = $request->headers->get(self::BAGGAGE_HEADER);

        $response = $event->getResponse();

        if (null !== $sentryTrace) {
            $response->headers->set(self::SENTRY_TRACE_HEADER, $sentryTrace);
        }

        if (null !== $baggage) {
            $response->headers->set(self::BAGGAGE_HEADER, $baggage);
        }
    }
}
