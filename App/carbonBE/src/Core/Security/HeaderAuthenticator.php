<?php

declare(strict_types=1);

namespace Core\Security;

use Core\Entity\User\User;
use Http\Client\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

final class HeaderAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly SessionManagementService $sessionManagementService,
        private readonly TokenStorageInterface $tokenStorage
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return null !== $request->headers->get('Authorization') && !$this->isAlreadyAuthenticated();
    }

    public function authenticate(Request $request): Passport
    {
        $token = $this->sessionManagementService->getTokenKeyFromHeader($request);
        if ('' === $token) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        $userToken = $this->sessionManagementService->getTokenFromTokenKey($token);
        if (null === $userToken) {
            throw new CustomUserMessageAuthenticationException('Invalid Api token');
        }

        try {
            /** @var User $user */
            $user = $userToken->getUser();
            $response = new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
        } catch (Exception) {
            throw new CustomUserMessageAuthenticationException('User not found');
        }

        return $response;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        throw new UnauthorizedHttpException('Bearer realm="'.$request->getHost().'", charset="UTF-8"', 'Unauthorized.');
    }

    protected function isAlreadyAuthenticated(): bool
    {
        $token = $this->tokenStorage->getToken();

        return $token instanceof TokenInterface && $token->getUser() instanceof UserInterface;
    }
}
