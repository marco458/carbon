<?php

namespace App\Service\FactorGas;

use App\Constant\SupportedFqcn;
use App\Entity\Factor\FactorGas;
use App\Entity\Gas\Gas;
use App\Repository\Factor\FactorGasRepository;
use App\Repository\Gas\GasRepository;

class FactorGasService
{
    public function __construct(
        private GasRepository $gasRepository,
        private FactorGasRepository $factorGasRepository,
    ) {
    }

    public function appendGasesToFactor(array $inputData, int $factorId, string $factorClassName): void
    {
        $factorFqcn = SupportedFqcn::mapClassNameToFqcn($factorClassName);
        $data = $this->filterData($inputData);

        foreach ($data as $gasFormula => $gasValue) {
            /** @var Gas $gas */
            $gas = $this->gasRepository->findOneBy(['formula' => $gasFormula]);

            $factorGas = new FactorGas();
            $factorGas->setgas($gas);
            $factorGas->setValue((float) $gasValue);
            $factorGas->setfactorId($factorId);
            $factorGas->setfactorFqcn($factorFqcn);
            $this->factorGasRepository->save($factorGas);
        }
    }

    public function filterData(array $data): array
    {
        $possibleDataKeys = [
            'CO2',
            'CH4',
            'N2O',
            'CO2eq',
            'CO2-upstream',
            'CH4-upstream',
            'N2O-upstream',
            'CO2eq-upstream',
            'CO2-combustion',
            'CH4-combustion',
            'N2O-combustion',
            'CO2eq-combustion',
            'CO2biog-combustion',
            'CO2-waste treatment',
            'CH4-waste treatment',
            'N2O-waste treatment',
            'CO2eq-waste treatment',
            'HFC-23',
            'HFC-32',
            'HFC-125',
            'HFC-134a',
            'HFC-143a',
        ];

        // filter $data so that only keys from $possibleDataKeys will remain inside array
        return array_intersect_key($data, array_flip($possibleDataKeys));
    }
}
