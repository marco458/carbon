<?php

declare(strict_types=1);

namespace Core\Service;

use Core\Entity\User\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Error\Error;

final readonly class UserEmailService
{
    public function __construct(
        private EmailService $emailService,
        private UrlGeneratorInterface $router,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Error
     */
    public function sendPasswordReset(User $user, ?string $callbackUrl): bool
    {
        if (null !== $callbackUrl) {
            // land on this page to reset password - JS landing page
            $passwordResetUrl = UrlHelper::external($callbackUrl, ['hash' => $user->getPasswordResetToken()]);
        } else {
            // land on Twig page - probably should not happen if using API! but let's leave it functional
            $passwordResetUrl = $this->router->generate(
                'password_reset_token',
                [
                    'token' => $user->getPasswordResetToken(),
                    // fix this, inject correct locale - but since this is in API context, need to see if this is really required or not?
                    '_locale' => 'en',
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        $data = $this->getDefaultData('Password reset', $user, ['passwordResetUrl' => $passwordResetUrl]);

        $templates = [
            'html' => 'email/userEmail/passwordReset.html.twig',
            'txt' => 'email/userEmail/passwordReset.txt.twig',
        ];

        $sent = $this->emailService->sendEmail($data, $templates);

        if (!$sent) {
            throw new HttpException(502, 'Failed to send email.');
        }

        return true;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Error
     */
    public function sendActivationEmail(User $user, ?string $callbackUrl): bool
    {
        if (null !== $callbackUrl) {
            // land on this page to reset password - JS landing page
            $activateAccountUrl = UrlHelper::external($callbackUrl, ['activationToken' => $user->getActivationToken()]);
        } else {
            // land on Twig page - probably should not happen if using API! but let's leave it functional
            $activateAccountUrl = $this->router->generate(
                'api_v1_users_activate',
                [
                    'activationToken' => $user->getActivationToken(),
                    // fix this, inject correct locale - but since this is in API context, need to see if this is really required or not?
                    '_locale' => 'en',
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        $data = $this->getDefaultData('Aktivacija računa', $user, ['activateAccountUrl' => $activateAccountUrl]);

        $templates = [
            'html' => 'email/userEmail/register.html.twig',
            'txt' => 'email/userEmail/register.txt.twig',
        ];

        $sent = $this->emailService->sendEmail($data, $templates);

        if (!$sent) {
            throw new HttpException(502, 'Failed to send email.');
        }

        return true;
    }

    private function getDefaultData(string $emailSubject, User $user, array $additionalContextData = []): array
    {
        $context = array_merge(['user' => $user], $additionalContextData);

        return [
            'subject' => $emailSubject,
            'to_address' => $user->getEmail(),
            'to_name' => $user->getFullName(),
            'context' => $context,
        ];
    }
}
