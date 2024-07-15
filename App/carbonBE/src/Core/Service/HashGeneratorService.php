<?php

declare(strict_types=1);

namespace Core\Service;

final class HashGeneratorService
{
    public const STRING_LENGTH = 64;

    public static function generate(): string
    {
        return bin2hex(random_bytes(self::STRING_LENGTH));
    }
}
