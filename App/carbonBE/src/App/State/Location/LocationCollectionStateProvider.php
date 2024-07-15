<?php

namespace App\State\Location;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\Location\LocationRepository;

class LocationCollectionStateProvider implements ProviderInterface
{
    public function __construct(
        private LocationRepository $repository,
        private iterable $collectionExtensions,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->repository->getFilteredCollection($this->collectionExtensions, $context, $operation);
    }
}
