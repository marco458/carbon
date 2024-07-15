<?php

namespace Core\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Carbon\Carbon;
use Core\Repository\UserRepository;

class UserActivate implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): null
    {
        $user = $this->userRepository->findUserByActivationToken($context['filters']['activationToken']);

        $user
            ->setActive(true)
            ->setActivationTokenExpiresAt(null)
            ->setActivationToken(null)
            ->setEmailConfirmedAt(Carbon::now());

        $this->userRepository->save($user);

        return null;
    }
}
