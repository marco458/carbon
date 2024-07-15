<?php

declare(strict_types=1);

namespace Core\State\Token;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Core\Entity\User\Token;
use Core\Exception\ApiNotFoundException;
use Core\Manager\TokenManager;
use Core\Repository\TokenRepository;

final readonly class TokenStateProvider implements ProviderInterface
{
    public function __construct(
        private TokenRepository $tokenRepository,
        private TokenManager $tokenManager,
    ) {
    }

    /**
     * @throws ApiNotFoundException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Token
    {
        /** @var Token|null $token */
        $token = $this->tokenRepository->findOneActiveByRefreshTokenKey($uriVariables['refreshToken']);

        if (!$token instanceof Token) {
            throw new ApiNotFoundException('RefreshToken not found');
        }

        $refreshToken = $this->tokenManager->refresh($token);

        $this->tokenRepository->save($refreshToken);

        return $refreshToken;
    }
}
