<?php

namespace App\State\Factor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\Factor\FactorUserRepository;

class FactorUserCollectionStateProvider implements ProviderInterface
{
    public function __construct(
        private FactorUserRepository $repository,
        private iterable $collectionExtensions,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->repository->getFilteredCollection($this->collectionExtensions, $context, $operation);
    }
}
