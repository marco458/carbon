<?php

declare(strict_types=1);

namespace Core\State\Token;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Core\Dto\Authentication\LoginRequest;
use Core\Entity\User\Token;
use Core\Entity\User\User;
use Core\Exception\ApiAuthorizationException;
use Core\Exception\ApiInvalidResourceException;
use Core\Manager\TokenManager;
use Core\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class TokenStateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private UserRepository $userRepository,
        private TokenManager $tokenManager,
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    /**
     * @throws ApiInvalidResourceException
     * @throws ApiAuthorizationException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Token
    {
        if (!$data instanceof LoginRequest) {
            throw new ApiInvalidResourceException('Expecting object contains email & password');
        }

        /** @var User|null $user */
        $user = $this->userRepository->findOneBy(['email' => $data->getEmail()]);
        if (!$user instanceof User || !$user->isActive()) {
            throw new ApiAuthorizationException('Access Denied. Invalid credentials or user deactivated.');
        }

        if (!$this->hasher->isPasswordValid($user, $data->getPassword())) {
            throw new ApiAuthorizationException('Access Denied. Invalid credentials or user deactivated.');
        }

        $token = $this->tokenManager->create($user);

        return $this->persistProcessor->process($token, $operation, $uriVariables, $context);
    }
}
