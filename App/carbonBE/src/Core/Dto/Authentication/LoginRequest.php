<?php

declare(strict_types=1);

namespace Core\Dto\Authentication;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class LoginRequest
{
    #[
        Assert\NotBlank(),
        Assert\Type(Types::STRING),
        Assert\Email(),
        Groups(['login'])
    ]
    private string $email;

    #[
        Assert\NotBlank(),
        Assert\Type(Types::STRING),
        Groups(['login'])
    ]
    private string $password;

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
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
