<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\Gas\GasRepository;

class GasCollectionStateProvider implements ProviderInterface
{
    public function __construct(
        private GasRepository $repository,
        private iterable $collectionExtensions,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->repository->getFilteredCollection($this->collectionExtensions, $context, $operation);
    }
}
