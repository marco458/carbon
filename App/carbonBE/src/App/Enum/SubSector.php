<?php

namespace App\Enum;

enum SubSector: string
{
    case PUBLIC_BOILER_HOUSES = 'Public boiler houses';
    case PUBLIC_HEATING_PLANTS = 'Public heating plants';
    case HEAT_PRODUCTION_SYSTEMS = 'Heat production systems';
    case AVERAGE_CONSUMPTION = 'average consumption';
    case RENEWABLE_SOURCES = 'renewable sources';
    case RENEWABLE_POWER_PLANT = 'renewable power plant';
    case FOSSIL_FUELS = 'fossil fuels';
    case BIOMASS_GREENHOUSE_GASES = 'biomass green houses';
    case BIOMASS_BIOGENIC_EMISSIONS = 'biomass biogenic emissions';

    public const OPTIONS = [
        self::PUBLIC_BOILER_HOUSES, self::PUBLIC_HEATING_PLANTS, self::HEAT_PRODUCTION_SYSTEMS,
        self::AVERAGE_CONSUMPTION, self::RENEWABLE_SOURCES, self::RENEWABLE_POWER_PLANT,
        self::FOSSIL_FUELS, self::BIOMASS_GREENHOUSE_GASES, self::BIOMASS_BIOGENIC_EMISSIONS,
    ];
}
