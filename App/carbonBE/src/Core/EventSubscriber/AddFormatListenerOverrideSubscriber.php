<?php

declare(strict_types=1);

namespace Core\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AddFormatListenerOverrideSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['override', 8], // AddFormat listener has priority 7
        ];
    }

    public function override(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $resourceClass = $request->attributes->get('_api_resource_class');
        $operationName = $request->attributes->get('_api_item_operation_name') ?? $request->attributes->get('_api_collection_operation_name');

        if ('login' === $operationName) {
            $request->headers->set('Accept', 'application/json'); // override text/html sent by browser to get json return type
        }
    }
}
