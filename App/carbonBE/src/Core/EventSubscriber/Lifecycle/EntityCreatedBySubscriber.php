<?php

declare(strict_types=1);

namespace Core\EventSubscriber\Lifecycle;

use Core\Exception\VerboseException;
use Core\Service\TraitHelper;
use Core\Traits\CreatedByTrait;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class EntityCreatedBySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [Events::prePersist];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (false === class_uses($entity::class)) {
            throw VerboseException::create('Error while attempting to set createdBy User for entity '.$entity::class);
        }

        if (!in_array(CreatedByTrait::class, TraitHelper::classUsesDeep($entity::class), true)) {
            return;
        }
        /* @phpstan-ignore-next-line */
        if (!is_null($entity->getCreatedBy())) {
            return;
        }
        /* @phpstan-ignore-next-line */
        $entity->setCreatedBy($this->security->getUser());
    }
}
