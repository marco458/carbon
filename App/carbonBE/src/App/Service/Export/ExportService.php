<?php

namespace App\Service\Export;

use App\Dto\CompleteEntry;
use App\Entity\Factor\FactorGas;
use App\Entity\Factor\FactorUser;
use App\Enum\Consumption;
use App\Enum\SectorName;
use App\Repository\Factor\FactorGasRepository;
use App\Repository\Factor\FactorUserRepository;
use App\Request\GenerateReportRequest;
use App\Service\FactorUser\FactorUserService;
use Core\Service\CurrentUserResolver;

class ExportService
{
    public function __construct(
        private CurrentUserResolver $currentUserResolver,
        private FactorUserRepository $factorUserRepository,
        private FactorGasRepository $factorGasRepository,
        private FactorUserService $factorUserService,
    ) {
    }

    public function getUserData(array $filters): array
    {
        $user = $this->currentUserResolver->resolve();
        $userFactors = $this->factorUserRepository->getFilteredData($filters);

        $userData = [];
        /** @var FactorUser $userFactor */
        foreach ($userFactors as $userFactor) {
            $savedActivity = $userFactor->getGasActivity();
            $factorGases = $this->factorGasRepository->filterByGasActivity($userFactor->getFactorFqcn(), $userFactor->getFactorId(), $savedActivity->value ?? null);

            /** @var FactorGas $factorGas */
            foreach ($factorGases as $factorGas) {
                $completeEntry = new CompleteEntry();
                $completeEntry->setUser($userFactor->getUser());

                // determine factor
                $factor = $this->factorUserService->determineFactor($userFactor->getFactorFqcn(), $userFactor->getFactorId());
                $completeEntry->setFactor($factor);

                $completeEntry->setFactorAmount($userFactor->getAmount());
                $completeEntry->setGasActivity($userFactor->getGasActivity());
                $completeEntry->setConsumption($userFactor->getConsumption());
                $completeEntry->setGas($factorGas->getGas());
                $completeEntry->setGasValue($factorGas->getValue());
                $userData[] = $completeEntry;
            }
        }

        return $userData;
    }

    public function calculateDistributionByScopes(array $userData = [], array $filters = []): array
    {
        if (!isset($userData['dummyCount']) && 0 === count($userData)) {
            $userData = $this->getUserData($filters);
        }

        $formulasToWatch = [
            'CO2',
            'CO2eq',
            'CO2eq-upstream',
            'CO2eq-combustion',
            'CO2biog-combustion',
            'CO2eq-waste treatment',
        ];

        $scopeCO2 = [];
        $scopeCO2['scope_1_value'] = 0;
        $scopeCO2['scope_2_value'] = 0;
        $scopeCO2['scope_3_value'] = 0;

        if (!isset($userData['dummyCount'])) {
            /** @var CompleteEntry $completeEntry */
            foreach ($userData as $completeEntry) {
                if (in_array($completeEntry->getGas()->getFormula(), $formulasToWatch, true)) {
                    $factor = $completeEntry->getFactor();
                    $unit = $factor->getUnit();

                    $CO2 = $completeEntry->getGasValue() * $completeEntry->getFactorAmount();

                    if (in_array($factor->getSector()->getName(), SectorName::SCOPE_1, true)) {
                        if (
                            Consumption::DIRECT === $completeEntry->getConsumption()
                        ) {
                            $scopeCO2['scope_1_value'] = $scopeCO2['scope_1_value'] + $CO2;
                        }
                    }
                    if (in_array($factor->getSector()->getName(), SectorName::SCOPE_2, true)) {
                        if (
                            Consumption::INDIRECT === $completeEntry->getConsumption()
                        ) {
                            $scopeCO2['scope_2_value'] = $scopeCO2['scope_2_value'] + $CO2;
                        }
                    }
                    if (in_array($factor->getSector()->getName(), SectorName::SCOPE_3, true)) {
                        if (
                            Consumption::INDIRECT === $completeEntry->getConsumption()
                        ) {
                            $scopeCO2['scope_3_value'] = $scopeCO2['scope_3_value'] + $CO2;
                        }
                    }
                }
            }
        }

        $totalCO2Kg = $scopeCO2['scope_1_value'] + $scopeCO2['scope_2_value'] + $scopeCO2['scope_3_value'];

        if (0 === $totalCO2Kg) {
            $scopeCO2['scope_1_percentage'] = 0;
            $scopeCO2['scope_2_percentage'] = 0;
            $scopeCO2['scope_3_percentage'] = 0;

            return $scopeCO2;
        }

        $scopeCO2['scope_1_percentage'] = $scopeCO2['scope_1_value'] / $totalCO2Kg * 100;
        $scopeCO2['scope_2_percentage'] = $scopeCO2['scope_2_value'] / $totalCO2Kg * 100;
        $scopeCO2['scope_3_percentage'] = $scopeCO2['scope_3_value'] / $totalCO2Kg * 100;

        return $scopeCO2;
    }

    public function prepareUserData(GenerateReportRequest $request): array
    {
        $filters = [];
        $filters['user_id'] = $this->currentUserResolver->resolve()->getId();

        if (null !== $request->getFromDate()) {
            $filters['from_date'] = $request->getFromDate();
        }

        if (null !== $request->getToDate()) {
            $filters['to_date'] = $request->getToDate();
        }

        if (null !== $request->getLocationId()) {
            $filters['location_id'] = $request->getLocationId();
        }

        $userData = $this->getUserData($filters);
        if (count($userData) === 0) {
            $userData['dummyCount'] = 0;
        }

        return $userData;
    }

    public function calculateDistributionBySectors(array $userData = [], array $filters = []): array
    {
        if (!isset($userData['dummyCount']) && 0 === count($userData)) {
            $userData = $this->getUserData($filters);
        }

        $formulasToWatch = [
            'CO2',
            'CO2eq',
            'CO2eq-upstream',
            'CO2eq-combustion',
            'CO2biog-combustion',
            'CO2eq-waste treatment',
        ];

        $sectorCO2 = [];
        $sectorCO2['sector_fuel_value'] = 0;
        $sectorCO2['sector_electrical_energy_value'] = 0;
        $sectorCO2['sector_heat_value'] = 0;
        $sectorCO2['sector_passenger_transportation_value'] = 0;
        $sectorCO2['sector_freight_transportation_value'] = 0;
        $sectorCO2['sector_land_conversion_value'] = 0;
        $sectorCO2['sector_waste_value'] = 0;
        $sectorCO2['sector_air_conditioning_value'] = 0;

        if (!isset($userData['dummyCount'])) {
            /** @var CompleteEntry $completeEntry */
            foreach ($userData as $completeEntry) {
                if (in_array($completeEntry->getGas()->getFormula(), $formulasToWatch, true)) {
                    $factor = $completeEntry->getFactor();

                    $CO2 = $completeEntry->getGasValue() * $completeEntry->getFactorAmount();

                    if (SectorName::FUEL === $factor->getSector()->getName()) {
                        $sectorCO2['sector_fuel_value'] = $sectorCO2['sector_fuel_value'] + $CO2;
                    }
                    if (SectorName::ELECTRICAL_ENERGY === $factor->getSector()->getName()) {
                        $sectorCO2['sector_electrical_energy_value'] = $sectorCO2['sector_electrical_energy_value'] + $CO2;
                    }
                    if (SectorName::HEAT === $factor->getSector()->getName()) {
                        $sectorCO2['sector_heat_value'] = $sectorCO2['sector_heat_value'] + $CO2;
                    }
                    if (SectorName::PASSENGER_TRANSPORTATION === $factor->getSector()->getName()) {
                        $sectorCO2['sector_passenger_transportation_value'] = $sectorCO2['sector_passenger_transportation_value'] + $CO2;
                    }
                    if (SectorName::FREIGHT_TRANSPORTATION === $factor->getSector()->getName()) {
                        $sectorCO2['sector_freight_transportation_value'] = $sectorCO2['sector_freight_transportation_value'] + $CO2;
                    }
                    if (SectorName::LAND_CONVERSION === $factor->getSector()->getName()) {
                        $sectorCO2['sector_land_conversion_value'] = $sectorCO2['sector_land_conversion_value'] + $CO2;
                    }
                    if (SectorName::WASTE === $factor->getSector()->getName()) {
                        $sectorCO2['sector_waste_value'] = $sectorCO2['sector_waste_value'] + $CO2;
                    }
                    if (SectorName::AIR_CONDITIONING === $factor->getSector()->getName()) {
                        $sectorCO2['sector_air_conditioning_value'] = $sectorCO2['sector_air_conditioning_value'] + $CO2;
                    }
                }
            }
        }

        $totalCO2Kg = 0;
        foreach ($sectorCO2 as $CO2) {
            $totalCO2Kg = $totalCO2Kg + $CO2;
        }

        if (0 === $totalCO2Kg) {
            $sectorCO2['sector_fuel_percentage'] = 0;
            $sectorCO2['sector_electrical_energy_percentage'] = 0;
            $sectorCO2['sector_heat_percentage'] = 0;
            $sectorCO2['sector_passenger_transportation_percentage'] = 0;
            $sectorCO2['sector_freight_transportation_percentage'] = 0;
            $sectorCO2['sector_land_conversion_percentage'] = 0;
            $sectorCO2['sector_waste_percentage'] = 0;
            $sectorCO2['sector_air_conditioning_percentage'] = 0;

            return $sectorCO2;
        }

        $sectorCO2['sector_fuel_percentage'] = $sectorCO2['sector_fuel_value'] / $totalCO2Kg * 100;
        $sectorCO2['sector_electrical_energy_percentage'] = $sectorCO2['sector_electrical_energy_value'] / $totalCO2Kg * 100;
        $sectorCO2['sector_heat_percentage'] = $sectorCO2['sector_heat_value'] / $totalCO2Kg * 100;
        $sectorCO2['sector_passenger_transportation_percentage'] = $sectorCO2['sector_passenger_transportation_value'] / $totalCO2Kg * 100;
        $sectorCO2['sector_freight_transportation_percentage'] = $sectorCO2['sector_freight_transportation_value'] / $totalCO2Kg * 100;
        $sectorCO2['sector_land_conversion_percentage'] = $sectorCO2['sector_land_conversion_value'] / $totalCO2Kg * 100;
        $sectorCO2['sector_waste_percentage'] = $sectorCO2['sector_waste_value'] / $totalCO2Kg * 100;
        $sectorCO2['sector_air_conditioning_percentage'] = $sectorCO2['sector_air_conditioning_value'] / $totalCO2Kg * 100;

        return $sectorCO2;
    }

    /* source https://ghgprotocol.org/sites/default/files/ghgp/Global-Warming-Potential-Values%20%28Feb%2016%202016%29_1.pdf
        CO2 x 1
        CH4 x 28
        N2O x 265
        HFC-23 x 12400
        HFC-32 x 677
        HFC-125 x 3170
        HFC-134a x 1300
        HFC-143a x 4800
    */
    public function calculateDistributionByGases(array $userData = [], array $filters = []): array
    {
        if (!isset($userData['dummyCount']) && 0 === count($userData)) {
            $userData = $this->getUserData($filters);
        }

        $formulasToWatch = [
            'CO2',
            'CH4',
            'N2O',
            'CO2-upstream',
            'CH4-upstream',
            'N2O-upstream',
            'CO2-combustion',
            'CH4-combustion',
            'N2O-combustion',
            'CO2biog-combustion',
            'CO2-waste treatment',
            'CH4-waste treatment',
            'N2O-waste treatment',
            'HFC-23',
            'HFC-32',
            'HFC-125',
            'HFC-134a',
            'HFC-143a',
        ];

        $gasCO2 = [
            'CO2_value' => 0,
            'CH4_value' => 0,
            'N2O_value' => 0,
            'CO2-upstream_value' => 0,
            'CH4-upstream_value' => 0,
            'N2O-upstream_value' => 0,
            'CO2-combustion_value' => 0,
            'CH4-combustion_value' => 0,
            'N2O-combustion_value' => 0,
            'CO2biog-combustion_value' => 0,
            'CO2-waste treatment_value' => 0,
            'CH4-waste treatment_value' => 0,
            'N2O-waste treatment_value' => 0,
            'HFC-23_value' => 0,
            'HFC-32_value' => 0,
            'HFC-125_value' => 0,
            'HFC-134a_value' => 0,
            'HFC-143a_value' => 0,
        ];

        $co2GasFormulas = [
            'CO2',
            'CO2-upstream',
            'CO2-combustion',
            'CO2biog-combustion',
            'CO2-waste treatment',
        ];

        $ch4GasFormulas = [
            'CH4',
            'CH4-upstream',
            'CH4-combustion',
            'CH4-waste treatment',
        ];

        $n2oGasFormulas = [
            'N2O',
            'N2O-upstream',
            'N2O-combustion',
            'N2O-waste treatment',
        ];

        $CO2Total = 0;
        $N2OTotal = 0;
        $CH4Total = 0;
        $otherTotal = 0;

        if (!isset($userData['dummyCount'])) {
            /** @var CompleteEntry $completeEntry */
            foreach ($userData as $completeEntry) {
                if (in_array($completeEntry->getGas()->getFormula(), $formulasToWatch, true)) {
                    $CO2 = $completeEntry->getGasValue() * $completeEntry->getFactorAmount();

                    if (in_array($completeEntry->getGas()->getFormula(), $co2GasFormulas, true)) {
                        $CO2 = $CO2 * 1;
                        $CO2Total = $CO2Total + $CO2;
                    } elseif (in_array($completeEntry->getGas()->getFormula(), $ch4GasFormulas, true)) {
                        $CO2 = $CO2 * 28;
                        $CH4Total = $CH4Total + $CO2;
                    } elseif (in_array($completeEntry->getGas()->getFormula(), $n2oGasFormulas, true)) {
                        $CO2 = $CO2 * 265;
                        $N2OTotal = $N2OTotal + $CO2;
                    } elseif ('HFC-23' === $completeEntry->getGas()->getFormula()) {
                        $CO2 = $CO2 * 12400;
                        $otherTotal = $otherTotal + $CO2;
                    } elseif ('HFC-32' === $completeEntry->getGas()->getFormula()) {
                        $CO2 = $CO2 * 677;
                        $otherTotal = $otherTotal + $CO2;
                    } elseif ('HFC-125' === $completeEntry->getGas()->getFormula()) {
                        $CO2 = $CO2 * 3170;
                        $otherTotal = $otherTotal + $CO2;
                    } elseif ('HFC-134a' === $completeEntry->getGas()->getFormula()) {
                        $CO2 = $CO2 * 1300;
                        $otherTotal = $otherTotal + $CO2;
                    } elseif ('HFC-143a' === $completeEntry->getGas()->getFormula()) {
                        $CO2 = $CO2 * 4800;
                        $otherTotal = $otherTotal + $CO2;
                    }

                    if (in_array($completeEntry->getGas()->getFormula(), $formulasToWatch, true)) {
                        $gasCO2[$completeEntry->getGas()->getFormula() . '_value'] = $gasCO2[$completeEntry->getGas()->getFormula() . '_value'] + $CO2;
                    }
                }
            }
        }

        $totalCO2Kg = array_sum($gasCO2);

        if (0 === $totalCO2Kg) {
            $data['CO2_total_value'] = 0;
            $data['CH4_total_value'] = 0;
            $data['N2O_total_value'] = 0;
            $data['other_total_value'] = 0;

            $data['CO2_total_percentage'] = 0;
            $data['CH4_total_percentage'] = 0;
            $data['N2O_total_percentage'] = 0;
            $data['other_total_percentage'] = 0;

            $data['all_gas_total_value'] = $totalCO2Kg;

            return $data;
        }

        $gasCO2Percentage = [];
        foreach ($gasCO2 as $key => $CO2) {
            $key = rtrim($key, '_value');
            $gasCO2Percentage[$key.'_percentage'] = ($CO2 / $totalCO2Kg) * 100;
        }

        $data = array_merge($gasCO2, $gasCO2Percentage);

        $CO2TotalPercentage = ($CO2Total / $totalCO2Kg) * 100;
        $N2OTotalPercentage = ($N2OTotal / $totalCO2Kg) * 100;
        $CH4TotalPercentage = ($CH4Total / $totalCO2Kg) * 100;
        $otherTotalPercentage = ($otherTotal / $totalCO2Kg) * 100;

        $data['CO2_total_value'] = $CO2Total;
        $data['CH4_total_value'] = $CH4Total;
        $data['N2O_total_value'] = $N2OTotal;
        $data['other_total_value'] = $otherTotal;

        $data['CO2_total_percentage'] = $CO2TotalPercentage;
        $data['CH4_total_percentage'] = $CH4TotalPercentage;
        $data['N2O_total_percentage'] = $N2OTotalPercentage;
        $data['other_total_percentage'] = $otherTotalPercentage;

        $data['all_gas_total_value'] = $totalCO2Kg;

        return $data;
    }
}
