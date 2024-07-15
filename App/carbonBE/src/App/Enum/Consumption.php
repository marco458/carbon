<?php

namespace App\Enum;

enum Consumption: string
{
    case DIRECT = 'direct';
    case INDIRECT = 'indirect';

    public const OPTIONS = [self::DIRECT, self::INDIRECT];
}
