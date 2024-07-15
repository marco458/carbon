<?php

namespace App\Enum\Location;

enum LocationLevel2: string
{
    case ADMINISTRATION = 'administracija';
    case PRODUCTION = 'proizvodnja';

    case OTHER = 'ostalo';

    public const OPTIONS = [self::ADMINISTRATION, self::PRODUCTION, self::OTHER];
}
