<?php

declare(strict_types=1);

namespace Core\Dto\Response\Authentication;

final class Login
{
    private string $token;

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): Login
    {
        $this->token = $token;

        return $this;
    }
}
