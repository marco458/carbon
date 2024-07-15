<?php

declare(strict_types=1);

namespace Core\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Carbon\Carbon;
use Core\Entity\User\Token;
use Core\Entity\User\User;
use Core\Exception\ApiInvalidResourceException;
use Core\Exception\ApiNotFoundException;
use Core\Exception\ApiValidationException;
use Core\Manager\TokenManager;
use Core\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserResetPasswordStateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private ProcessorInterface $persistProcessor,
        private UserRepository $userRepository,
        private TokenManager $tokenManager
    ) {
    }

    /**
     * @throws ApiValidationException
     * @throws ApiNotFoundException
     * @throws ApiInvalidResourceException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Token
    {
        if (!$data instanceof User) {
            // let's tell phpstan that this is a User object
            throw new ApiInvalidResourceException('Expecting resource to be User');
        }
        $user = $this->userRepository->findOneByPasswordResetToken($data->getPasswordResetToken());
        if (!$user instanceof User || null === $data->getPasswordResetToken()) {
            throw new ApiNotFoundException('User not found');
        }

        if ($user->getPasswordResetTokenExpiresAt() < Carbon::now()) {
            throw new ApiValidationException('Reset password token expired, use the reset password option again.');
        }

        if (null !== $data->getPlaintextPassword()) {
            $hashedPassword = $this->userPasswordHasher->hashPassword($user, trim($data->getPlaintextPassword()));
            $user->setPassword($hashedPassword);
            $user->setPasswordResetToken(null);
        }

        if (!$user->isActive() && !(bool) $user->getEmailConfirmedAt()) {
            $user->setActive(true);
            $user->setEmailConfirmedAt(Carbon::now());
            $user->setActivationToken(null);
            $user->setActivationTokenExpiresAt(null);
        }
        $this->persistProcessor->process($user, $operation, $uriVariables, $context);

        // issue a token for user to auto login
        $token = $this->tokenManager->create($user);

        return $this->persistProcessor->process($token, $operation, $uriVariables, $context);
    }
}
