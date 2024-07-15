<?php

declare(strict_types=1);

namespace Core\Dto\Authentication;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class RequestPasswordReset
{
    #[
        Assert\NotBlank(),
        Assert\Type(Types::STRING),
        Assert\Email(),
        Groups(['password-reset:create']),
    ]
    private string $email;

    #[
        Assert\Url(),
        Groups(['password-reset:create'])
    ]
    private ?string $callbackUrl = null;

    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(?string $callbackUrl): void
    {
        $this->callbackUrl = $callbackUrl;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
