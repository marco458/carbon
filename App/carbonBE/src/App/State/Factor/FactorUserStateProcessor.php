<?php

namespace App\State\Factor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Constant\SupportedFqcn;
use App\Entity\Factor\FactorUser;
use Core\Service\CurrentUserResolver;

class FactorUserStateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private CurrentUserResolver $currentUserResolver,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        /** @var FactorUser $factorUser */
        $factorUser = $data;

        $factorUser->setFactorFqcn(SupportedFqcn::mapClassNameToFqcn($factorUser->getFactorFqcn()));

        $user = $this->currentUserResolver->resolve();
        $factorUser->setUser($user);

        $this->persistProcessor->process($factorUser, $operation, $uriVariables, $context);
    }
}
