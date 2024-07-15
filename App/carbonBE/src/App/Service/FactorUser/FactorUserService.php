<?php

namespace App\Service\FactorUser;

use App\Constant\SupportedFqcn;
use App\Entity\AirConditioning\AirConditioning;
use App\Entity\ElectricalEnergy\ElectricalEnergy;
use App\Entity\Factor\Factor;
use App\Entity\Factor\FactorUser;
use App\Entity\Fuel\Fuel;
use App\Entity\Heat\Heat;
use App\Entity\LandConversion\LandConversion;
use App\Entity\Transportation\FreightTransportation;
use App\Entity\Transportation\PassengerTransportation;
use App\Entity\Waste\Waste;
use App\Enum\Consumption;
use App\Enum\GasActivity;
use App\Repository\AirConditioning\AirConditioningRepository;
use App\Repository\ElectricalEnergy\ElectricalEnergyRepository;
use App\Repository\Factor\FactorUserRepository;
use App\Repository\Fuel\FuelRepository;
use App\Repository\Heat\HeatRepository;
use App\Repository\LandConversion\LandConversionRepository;
use App\Repository\Transportation\FreightTransportationRepository;
use App\Repository\Transportation\PassengerTransportationRepository;
use App\Repository\Waste\WasteRepository;
use Carbon\Carbon;
use Core\Repository\UserRepository;

class FactorUserService
{
    public function __construct(
        private UserRepository $userRepository,
        private FactorUserRepository $factorUserRepository,
        private WasteRepository $wasteRepository,
        private LandConversionRepository $landConversionRepository,
        private AirConditioningRepository $airConditioningRepository,
        private FreightTransportationRepository $freightTransportationRepository,
        private PassengerTransportationRepository $passengerTransportationRepository,
        private HeatRepository $heatRepository,
        private ElectricalEnergyRepository $electricalEnergyRepository,
        private FuelRepository $fuelRepository,
    ) {
    }

    public function createEntryForUser(
        int $userId,
        int $factorId,
        string $className,
        float $amount,
        int $year,
        ?GasActivity $gasActivity = null,
        ?Consumption $consumption = null,
        string $unit = '',
    ): void {
        $factorFqcn = SupportedFqcn::mapClassNameToFqcn($className);
        $user = $this->userRepository->find($userId);
        if (null === $user) {
            throw new \Exception('factor_user.user_not_found');
        }

        $entry = new FactorUser();
        $entry = $entry->setUser($user);
        $entry->setFactorId($factorId);
        $entry->setFactorFqcn($factorFqcn);
        $entry->setAmount($amount);
        $entry->setDate(Carbon::create($year, 1, 1));
        $entry->setGasActivity($gasActivity);
        $entry->setConsumption($consumption);
        $entry->setUnit($unit);

        $this->factorUserRepository->save($entry);
    }

    public function determineFactor(string $factorFqcn, int $factorId): Factor
    {
        switch ($factorFqcn) {
            case Waste::class:
                $factor = $this->wasteRepository->find($factorId);
                break;
            case LandConversion::class:
                $factor = $this->landConversionRepository->find($factorId);
                break;
            case AirConditioning::class:
                $factor = $this->airConditioningRepository->find($factorId);
                break;
            case FreightTransportation::class:
                $factor = $this->freightTransportationRepository->find($factorId);
                break;
            case PassengerTransportation::class:
                $factor = $this->passengerTransportationRepository->find($factorId);
                break;
            case Heat::class:
                $factor = $this->heatRepository->find($factorId);
                break;
            case ElectricalEnergy::class:
                $factor = $this->electricalEnergyRepository->find($factorId);
                break;
            case Fuel::class:
                $factor = $this->fuelRepository->find($factorId);
                break;
            default:
                $factor = null;
                break;
        }

        if (null === $factor) {
            throw new \Exception('factor.not_found');
        }

        return $factor;
    }
}
