<?php

declare(strict_types=1);

namespace Core\Security;

use Carbon\Carbon;
use Core\Entity\User\Token;
use Core\Entity\User\User;
use Core\Manager\TokenManager;
use Core\Repository\TokenRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class SessionManagementService
{
    public const COOKIE_NAME = 'session_token';
    public const HEADER_NAME = 'Authorization';

    /**
     * Array of Token objects. token => Token.
     */
    private array $tokens = [];

    public function __construct(
        private readonly TokenRepository $tokenRepository,
        private readonly TokenManager $tokenManager
    ) {
    }

    public function startSession(TokenInterface $token, Response $response): Token
    {
        /** @var User $user */
        $user = $token->getUser();

        $userToken = $this->tokenManager->create($user);
        $tokenKey = $userToken->getTokenKey();

        $response->headers->setCookie(\Symfony\Component\HttpFoundation\Cookie::create(self::COOKIE_NAME, $tokenKey));

        return $userToken;
    }

    public function getTokenKeyFromCookie(Request $request): string
    {
        return trim($request->cookies->get(self::COOKIE_NAME, ''));
    }

    public function getTokenKeyFromHeader(Request $request): string
    {
        /** @var string $headerValue */
        $headerValue = $request->headers->get(self::HEADER_NAME, '');

        $explodedHeaderValue = explode(' ', trim($headerValue), 2);

        if (2 === \count($explodedHeaderValue)) {
            return $explodedHeaderValue[1];
        }

        // if "Bearer " is missing, use whatever we got as the token
        return $explodedHeaderValue[0];
    }

    public function getTokenFromTokenKey(string $tokenKey): Token|null
    {
        if ('' === $tokenKey) {
            return null;
        }

        if (isset($this->tokens[$tokenKey])) {
            return $this->tokens[$tokenKey];
        }

        $token = $this->tokenRepository->getFromTokenKey($tokenKey);

        if ($token instanceof Token) {
            if (
                $token->getLastActiveDate() instanceof \DateTimeInterface &&
                $token->getLastActiveDate()->getTimestamp() < (new Carbon())->getTimestamp()
            ) {
                // update last active date
                $token->setLastActiveDate(new Carbon('now'));
                $this->tokenRepository->flush(); // method does not require parameter
            }

            $this->tokens[$tokenKey] = $token;
        }

        return $token;
    }

    public function endSession(Request $request, Response $response): void
    {
        if ($request->cookies->has(self::COOKIE_NAME)) {
            $response->headers->clearCookie(self::COOKIE_NAME);
        }

        $request->getSession()->invalidate();

        $tokenKey = $this->getTokenKeyFromCookie($request);
        $token = $this->getTokenFromTokenKey($tokenKey);

        if ($token instanceof Token) {
            $this->tokenRepository->remove($token);
        }
    }
}
