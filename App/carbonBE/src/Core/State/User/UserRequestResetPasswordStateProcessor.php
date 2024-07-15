<?php

declare(strict_types=1);

namespace Core\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Carbon\Carbon;
use Core\Dto\Authentication\RequestPasswordReset;
use Core\Entity\User\User;
use Core\Exception\ApiInvalidResourceException;
use Core\Exception\ApiNotFoundException;
use Core\Repository\UserRepository;
use Core\Service\HashGeneratorService;
use Core\Service\UrlHelper;
use Core\Service\UserEmailService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\Error;

final readonly class UserRequestResetPasswordStateProcessor implements ProcessorInterface
{
    public const MINUTES_TO_EXPIRATION = 5;

    public function __construct(
        private ProcessorInterface $persistProcessor,
        private UserRepository $userRepository,
        private UserEmailService $emailService
    ) {
    }

    /**
     * @throws Error
     * @throws NonUniqueResultException
     * @throws TransportExceptionInterface
     * @throws ApiInvalidResourceException
     * @throws ApiNotFoundException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if (!$data instanceof RequestPasswordReset) {
            throw new ApiInvalidResourceException('Expecting object should contain email & callbackUrl');
        }

        $user = $this->userRepository->findUserPasswordReset($data->getEmail());

        $callbackUrl = (string) $data->getCallbackUrl();

        UrlHelper::validateUrl($callbackUrl);

        $resetToken = HashGeneratorService::generate();
        $user->setPasswordResetToken($resetToken)
            ->setPasswordResetTokenExpiresAt(Carbon::now()->addMinutes(self::MINUTES_TO_EXPIRATION));

        // potentially this can be moved to data persister?
        if (!$this->emailService->sendPasswordReset($user, $callbackUrl)) {
            throw new HttpException(Response::HTTP_GONE, 'Email message failed to send.');
        }

        return $this->persistProcessor->process($user, $operation, $uriVariables, $context);
    }
}
