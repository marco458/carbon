<?php

namespace App\State\Location;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Location\Location;
use Core\Service\CurrentUserResolver;

class LocationStateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private CurrentUserResolver $userResolver,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        /** @var Location $location */
        $location = $data;
        $location->setUser($this->userResolver->resolve());
        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
