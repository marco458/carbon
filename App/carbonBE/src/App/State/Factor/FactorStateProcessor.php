<?php

namespace App\State\Factor;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Constant\SupportedFqcn;
use App\Entity\Factor\FactorGas;
use App\Entity\Gas\Gas;
use App\Repository\Factor\FactorGasRepository;

class FactorStateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private IriConverterInterface $iriConverter,
        private FactorGasRepository $factorGasRepository
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $classFqcn = SupportedFqcn::mapClassNameToFqcn((string) $data->getClassName());

        $gasesIri = $data->getGases();
        $gases = [];

        foreach ($gasesIri as $iri) {
            $gas = $this->iriConverter->getResourceFromIri($iri['gas']);
            $gases[] = ['gas' => $gas, 'value' => $iri['value']];
        }

        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        $factorId = $data->getId();

        /** @var Gas $gas */
        foreach ($gases as $gas) {
            $factorGas = new FactorGas();
            $factorGas->setgas($gas['gas']);
            $factorGas->setfactorFqcn($classFqcn);
            $factorGas->setfactorId($factorId);
            $factorGas->setValue($gas['value']);

            $this->factorGasRepository->save($factorGas);
        }
    }
}
