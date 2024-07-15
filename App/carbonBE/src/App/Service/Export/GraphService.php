<?php

namespace App\Service\Export;

class GraphService
{
    public function __construct(
        private ExportService $exportService
    ) {
    }

    public function prepareGraphScopeValue(array $scopeData): array
    {
        if (0 === count($scopeData)) {
            $scopeData = $this->exportService->calculateDistributionByScopes();
        }

        return [
            $scopeData['scope_1_value'],
            $scopeData['scope_2_value'],
            $scopeData['scope_3_value'],
        ];
    }

    public function prepareGraphScopePercentage(array $scopeData): array
    {
        if (0 === count($scopeData)) {
            $scopeData = $this->exportService->calculateDistributionByScopes();
        }

        return [
            $scopeData['scope_1_percentage'],
            $scopeData['scope_2_percentage'],
            $scopeData['scope_3_percentage'],
        ];
    }

    public function prepareGraphSectorValue(array $sectorData): array
    {
        if (0 === count($sectorData)) {
            $sectorData = $this->exportService->calculateDistributionBySectors();
        }

        return [
            round($sectorData['sector_fuel_value'] / 1000, 2),
            round($sectorData['sector_electrical_energy_value'] / 1000, 2),
            round($sectorData['sector_heat_value'] / 1000, 2),
            round($sectorData['sector_passenger_transportation_value'] / 1000, 2),
            round($sectorData['sector_freight_transportation_value'] / 1000, 2),
            round($sectorData['sector_air_conditioning_value'] / 1000, 2),
            round($sectorData['sector_land_conversion_value'] / 1000, 2),
            round($sectorData['sector_waste_value'] / 1000, 2),
        ];
    }

    public function prepareGraphSectorPercentage(array $sectorData): array
    {
        if (0 === count($sectorData)) {
            $sectorData = $this->exportService->calculateDistributionBySectors();
        }

        return [
            $sectorData['sector_fuel_percentage'],
            $sectorData['sector_electrical_energy_percentage'],
            $sectorData['sector_heat_percentage'],
            $sectorData['sector_passenger_transportation_percentage'],
            $sectorData['sector_freight_transportation_percentage'],
            $sectorData['sector_air_conditioning_percentage'],
            $sectorData['sector_land_conversion_percentage'],
            $sectorData['sector_waste_percentage'],
        ];
    }

    public function prepareGraphGasValue(array $gasData): array
    {
        if (0 === count($gasData)) {
            $gasData = $this->exportService->calculateDistributionByGases();
        }

        return [
            round($gasData['CO2_total_value'] / 1000, 2),
            round($gasData['CH4_total_value'] / 1000, 2),
            round($gasData['N2O_total_value'] / 1000, 2),
            round($gasData['other_total_value'] / 1000, 2),
        ];
    }

    public function prepareGraphGasPercentage(array $gasData): array
    {
        if (0 === count($gasData)) {
            $gasData = $this->exportService->calculateDistributionByGases();
        }

        return [
            $gasData['CO2_total_percentage'],
            $gasData['CH4_total_percentage'],
            $gasData['N2O_total_percentage'],
            $gasData['other_total_percentage'],
        ];
    }
}
