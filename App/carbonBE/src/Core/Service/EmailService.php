<?php

declare(strict_types=1);

namespace Core\Service;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\Error as TwigError;

final class EmailService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly string $mailFrom,
        private readonly string $mailReplyTo,
    ) {
    }

    public function sendEmail(array $data, array $templates): bool
    {
        if (!$this->logger instanceof LoggerInterface) {
            throw new ServiceNotFoundException('No logger has been assigned to EmailService. Please check monolog configuration');
        }

        $email = (new Email())
            ->to(new Address($data['to_address'], $data['to_name']))
            ->from($data['mail_from'] ?? $this->mailFrom)
            ->replyTo($data['mail_reply_to'] ?? $this->mailReplyTo)
            ->subject($data['subject']);

        if (isset($data['cc']) && false !== $data['cc']) {
            $email->cc($data['cc']);
        }

        if (isset($data['bcc']) && false !== $data['bcc']) {
            $email->bcc($data['bcc']);
        }

        try {
            $textBody = $this->twig->render($templates['txt'], $data['context']);
            $htmlBody = $this->twig->render($templates['html'], $data['context']);
        } catch (TwigError $exception) {
            $this->logger->error('Failed to render email.', ['message' => $exception->getMessage(), 'data' => $data]);
            if ('dev' === getenv('APP_ENV')) {
                throw $exception;
            }

            return false;
        }

        $email
            ->text($textBody)
            ->html($htmlBody);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            if ('dev' === getenv('APP_ENV')) {
                throw $e;
            }

            $this->logger->error(
                'Failed to send email.',
                ['data' => $data, 'exception' => $e->getMessage()]
            );

            return false;
        }

        return true;
    }
}
