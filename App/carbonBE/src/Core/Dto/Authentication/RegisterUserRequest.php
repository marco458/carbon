<?php

declare(strict_types=1);

namespace Core\Dto\Authentication;

use Core\Entity\User\User;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class RegisterUserRequest
{
    #[
        Assert\Valid(),
        Assert\NotBlank(),
        Assert\Type(User::class),
        Groups(['register']),
    ]
    private User $user;

    #[
        Assert\Url(),
        Groups(['register'])
    ]
    private ?string $callbackUrl = null;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getCallbackUrl(): ?string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(?string $callbackUrl): void
    {
        $this->callbackUrl = $callbackUrl;
    }
}
