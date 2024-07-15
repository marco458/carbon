<?php

namespace App\Enum;

enum GasActivity: string
{
    case UPSTREAM = 'upstream';
    case COMBUSTION = 'combustion';
    case WASTE_TREATMENT = 'waste treatment';

    public const OPTIONS = [self::UPSTREAM, self::COMBUSTION, self::WASTE_TREATMENT];
}
