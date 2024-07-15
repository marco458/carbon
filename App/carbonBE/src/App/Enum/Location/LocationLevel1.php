<?php

namespace App\Enum\Location;

enum LocationLevel1: string
{
    case PROPERTY = 'nekretnine';
    case MOVABLE = 'pokretnine';
    case OTHER = 'ostalo';

    public const OPTIONS = [self::PROPERTY, self::MOVABLE, self::OTHER];
}
