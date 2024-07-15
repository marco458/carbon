<?php

declare(strict_types=1);

namespace Core\EventSubscriber;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Core\DataTransformer\Core\ControllerResult\PaginatedResultTransformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SerializeListenerOverrideSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['addPaginationMetadata', EventPriorities::POST_SERIALIZE],
            ],
        ];
    }

    public function addPaginationMetadata(ViewEvent $event): void
    {
        if (!$event->getRequest()->attributes->has('data')) {
            return;
        }

        $data = $event->getRequest()->attributes->get('data');

        if (!$data instanceof Paginator) {
            return;
        }

        $controllerResult = $event->getControllerResult();
        if (!is_string($controllerResult)) {
            return; // Courtesy of phpstan
        }

        $request = $event->getRequest();
        $result = (new PaginatedResultTransformer($request, $data))->transform($controllerResult);

        $event->setControllerResult($result);
    }
}
