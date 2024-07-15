<?php

declare(strict_types=1);

namespace Core\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Core\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class UserItemStateProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): UserInterface|null
    {
        if (($operation->getName() ?? '') === 'api_v1_me_get') {
            return $this->security->getUser();
        }

        /* @phpstan-ignore-next-line */
        return $this->userRepository->find($uriVariables['id']);
    }
}
