<?php

declare(strict_types=1);

namespace Core\Repository;

use Carbon\Carbon;
use Core\Entity\User\Token;

final class TokenRepository extends BaseRepository
{
    public const ENTITY_CLASS_NAME = Token::class;

    public function getFromTokenKey(string $tokenKey): ?Token
    {
        /** @var Token|null $token */
        $token = $this->findOneBy([
            'tokenKey' => $tokenKey,
        ]);

        if (!$token instanceof Token) {
            return null;
        }

        $expiresAt = $token->getExpiresAt();
        if ($expiresAt instanceof \DateTimeInterface && $expiresAt <= Carbon::now()) {
            return null;
        }

        return $token;
    }

    public function findOneActiveByRefreshTokenKey(mixed $refreshTokenKey): ?Token
    {
        /** @var Token|null $token */
        $token = $this->findOneBy([
            'refreshTokenKey' => $refreshTokenKey,
        ]);

        if (!$token instanceof Token) {
            return null;
        }

        $refreshExpiresAt = $token->getRefreshExpiresAt();
        if ($refreshExpiresAt instanceof \DateTimeInterface && $refreshExpiresAt <= Carbon::now()) {
            // token has expired
            return null;
        }

        return $token;
    }

    /**
     * Purge all expired tokens from database
     *  - will remove all tokens where expired date of refresh token has passed
     *  - careful when running this on databases with millions of records.
     */
    public function purgeExpiredTokens(): void
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->delete('App:User\Token', 't')
            ->where('t.refreshExpiresAt IS NULL OR t.refreshExpiresAt <= :now')
            ->setParameter('now', Carbon::now());

        $qb->getQuery()->execute();
    }
}
