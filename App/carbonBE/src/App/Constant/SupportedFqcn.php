<?php

namespace App\Constant;

use App\Entity\AirConditioning\AirConditioning;
use App\Entity\ElectricalEnergy\ElectricalEnergy;
use App\Entity\Fuel\Fuel;
use App\Entity\Heat\Heat;
use App\Entity\LandConversion\LandConversion;
use App\Entity\Transportation\FreightTransportation;
use App\Entity\Transportation\PassengerTransportation;
use App\Entity\Waste\Waste;

class SupportedFqcn
{
    private const CLASS_NAME_TO_FQCN_NAME_MAPPING = [
        'waste' => Waste::class,
        'land' => LandConversion::class,
        'air' => AirConditioning::class,
        'freight' => FreightTransportation::class,
        'passenger' => PassengerTransportation::class,
        'heat' => Heat::class,
        'energy' => ElectricalEnergy::class,
        'fuel' => Fuel::class,
    ];

    public static function mapClassNameToFqcn(string $className): string
    {
        $name = self::CLASS_NAME_TO_FQCN_NAME_MAPPING[$className] ?? '';

        if ('' === $name) {
            throw new \Exception('supported_fqcn.invalid_mapping');
        }

        return $name;
    }
}
