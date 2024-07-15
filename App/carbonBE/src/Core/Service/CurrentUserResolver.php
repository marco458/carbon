<?php

namespace Core\Service;

use Core\Entity\User\User;
use Core\Repository\TokenRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class CurrentUserResolver
{
    public function __construct(
        private RequestStack $requestStack,
        private TokenRepository $tokenRepository,
    ) {
    }

    public function resolve(?Request $request = null): User
    {
        if (null === $request) {
            /** @var Request $request */
            $request = $this->requestStack->getCurrentRequest();
        }

        $token = (string) $request->headers->get('Authorization');

        $tokenUser = $this->tokenRepository->findOneBy(['tokenKey' => $token]);

        return $tokenUser->getUser();
    }
}
