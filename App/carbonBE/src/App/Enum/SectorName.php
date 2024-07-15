<?php

namespace App\Enum;

enum SectorName: string
{
    case FUEL = 'fuel';
    case ELECTRICAL_ENERGY = 'electrical energy';
    case HEAT = 'heat';
    case PASSENGER_TRANSPORTATION = 'passenger transportation';
    case FREIGHT_TRANSPORTATION = 'freight transportation';
    case LAND_CONVERSION = 'land conversion';
    case WASTE = 'waste';
    case AIR_CONDITIONING = 'air conditioning';

    public const OPTIONS = [
        self::FUEL,
        self::ELECTRICAL_ENERGY,
        self::HEAT,
        self::PASSENGER_TRANSPORTATION,
        self::FREIGHT_TRANSPORTATION,
        self::LAND_CONVERSION,
        self::WASTE,
        self::AIR_CONDITIONING,
    ];

    public const SCOPE_1 = [
        self::LAND_CONVERSION,
        self::ELECTRICAL_ENERGY,
        self::FUEL,
        self::PASSENGER_TRANSPORTATION,
        self::FREIGHT_TRANSPORTATION,
    ];

    public const SCOPE_2 = [
        self::ELECTRICAL_ENERGY, self::HEAT, self::AIR_CONDITIONING,
    ];

    public const SCOPE_3 = [
        self::WASTE, self::FUEL, self::PASSENGER_TRANSPORTATION, self::FREIGHT_TRANSPORTATION,
    ];
}
