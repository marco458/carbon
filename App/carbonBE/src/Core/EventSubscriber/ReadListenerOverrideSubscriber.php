<?php

declare(strict_types=1);

namespace Core\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use Core\Entity\User\Token;
use Core\Entity\User\User;
use Core\Repository\TokenRepository;
use Core\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * To override default behavior of api platform read listener
 * Could be used for identifier conversion (see refresh token example, converted refreshToken -> id).
 */
final readonly class ReadListenerOverrideSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenRepository $tokenRepository,
        private UserRepository $userRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['override', EventPriorities::PRE_READ],
        ];
    }

    public function override(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $attributes = $request->attributes->all();

        if (!isset($attributes['_api_resource_class'])) {
            return;
        }

        $isCollectionOperation = isset($attributes['_api_operation_name']);

        if ($isCollectionOperation) {
            // probably won't have those?
            $this->applyCollectionOperationOverrides($attributes, $request);
        } else {
            $this->applyItemOperationOverrides($attributes, $request);
        }
    }

    private function applyCollectionOperationOverrides(array $attributes, Request $request): void
    {
    }

    private function applyItemOperationOverrides(array $attributes, Request $request): void
    {
        if (Token::class === $attributes['_api_resource_class'] && 'refresh' === $attributes['_api_operation_name']) {
            $request->attributes->set('id', $this->tokenRepository->findOneActiveByRefreshTokenKey($request->attributes->get('refreshToken'))?->getId());
        }

        $hash = $request->attributes->get('hash');
        if (User::class === $attributes['_api_resource_class'] && 'reset_password' === $attributes['_api_operation_name']
                && is_string($hash)) {
            $request->attributes->set('id', $this->userRepository->findOneByPasswordResetToken($hash)?->getId());
        }
    }
}
