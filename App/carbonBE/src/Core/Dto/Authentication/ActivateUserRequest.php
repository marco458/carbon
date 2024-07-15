<?php

declare(strict_types=1);

namespace Core\Dto\Authentication;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class ActivateUserRequest
{
    #[
        Assert\NotBlank(),
        Assert\Type(Types::STRING),
        Groups(['activate'])
    ]
    private ?string $activationToken = '';

    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    public function setActivationToken(?string $activationToken): void
    {
        $this->activationToken = $activationToken;
    }
}
