<?php

declare(strict_types=1);

namespace Core\Manager;

use ApiPlatform\Api\IriConverterInterface;
use Carbon\CarbonImmutable as Carbon;
use Core\Entity\User\Token;
use Core\Entity\User\User;
use Core\Factory\EntityFactory;
use Core\Repository\TokenRepository;
use Core\Service\HashGeneratorService;

final readonly class TokenManager
{
    private const EXPIRATION_DAYS = 60;

    public function __construct(
        private TokenRepository $repository,
        private EntityFactory $entityFactory,
        private IriConverterInterface $iriConverter,
    ) {
    }

    /**
     * Creates token and refresh token pair with following expiration times:
     * - token - 60 days
     * - refresh token -double the token expiration time.
     */
    public function create(User $user, bool $save = false): Token
    {
        $tokenKey = HashGeneratorService::generate();
        $refreshTokenKey = HashGeneratorService::generate();

        $now = Carbon::now();
        $data = [
            'created_at' => $now,
            'user' => $this->iriConverter->getIriFromResource($user),
            'token_key' => $tokenKey,
            'refresh_token_key' => $refreshTokenKey,
            'last_active_date' => $now,
            'expires_at' => $now->addDays(self::EXPIRATION_DAYS),
            'refresh_expires_at' => $now->addDays(2 * self::EXPIRATION_DAYS),
        ];

        /** @var Token $token */
        $token = $this->entityFactory->createFromArray($data, Token::class, ['token:create']);

        if ($save) {
            $this->repository->save($token);
        }

        return $token;
    }

    public function refresh(Token $token): Token
    {
        /** @var User $user */
        $user = $token->getUser();
        $newToken = $this->create($user);
        $this->destroy($token);

        return $newToken;
    }

    public function destroy(Token $token): void
    {
        $this->repository->remove($token);
    }

    public function destroyAll(User $user): void
    {
        foreach ($user->getTokens() as $token) {
            /* @phpstan-ignore-next-line */
            $this->repository->remove($token); // Same code with the destroy() above which does not trigger error from phpstan.
        }
    }
}
