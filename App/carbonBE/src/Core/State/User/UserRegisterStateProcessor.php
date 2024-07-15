<?php

declare(strict_types=1);

namespace Core\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Carbon\Carbon;
use Core\Dto\Authentication\RegisterUserRequest;
use Core\Entity\User\User;
use Core\Exception\ApiBadRequestException;
use Core\Service\HashGeneratorService;
use Core\Service\UserEmailService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Twig\Error\Error;

final readonly class UserRegisterStateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private UserEmailService $emailService,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    /**
     * @throws Error
     * @throws TransportExceptionInterface
     * @throws ApiBadRequestException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if (!($data instanceof RegisterUserRequest) || 'api_v1_users_register' !== $operation->getName()) {
            throw new ApiBadRequestException('Unable to process, invalid operation.');
        }

        $callbackUrl = $data->getCallbackUrl();
        $user = $data->getUser();

        $user
            ->setActivationToken(HashGeneratorService::generate())
            ->setActivationTokenExpiresAt(Carbon::now()->addMinutes(User::ACTIVATION_TOKEN_EXPIRATION));

        $hashedPassword = $this->userPasswordHasher->hashPassword($user, trim((string) $user->getPlaintextPassword()));
        $user
            ->setPassword($hashedPassword)
            ->setPasswordResetToken(null);

        // potentially this can be moved to data persister?
        if (!$this->emailService->sendActivationEmail($user, $callbackUrl)) {
            throw new HttpException(Response::HTTP_GONE, 'Failed sending activation email!');
        }

        return $this->persistProcessor->process($user, $operation, $uriVariables, $context);
    }
}
