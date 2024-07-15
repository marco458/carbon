<?php

declare(strict_types=1);

namespace Core\State\User;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Core\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class UserCollectionStateProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private RequestStack $requestStack,
        private iterable $collectionExtensions,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable|object|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $request = $this->requestStack->getCurrentRequest();

            /** @var string $role */
            $role = $request?->attributes->get('role', '');

            return $this->userRepository->findAllByRole($role, $this->collectionExtensions, $context, $operation);
        }

        return null;
    }
}
