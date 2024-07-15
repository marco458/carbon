<?php

namespace App\Entity\Fuel;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Factor\Factor;
use App\Enum\SubSector;
use App\Filters\BaseFilters;
use App\Repository\Fuel\FuelRepository;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ORM\Table(name: 'fuels'),
    ORM\Entity(repositoryClass: FuelRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/fuels',
            openapiContext: [
                'parameters' => BaseFilters::LIST,
            ],
            normalizationContext: [
                'groups' => ['fuel:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_fuels_index'
        ),
        new Get(
            uriTemplate: '/fuels/{id}',
            normalizationContext: [
                'groups' => ['fuel:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_fuels_get'
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_ADMIN")',
)]
class Fuel extends Factor implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['fuel:get']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(name: 'fuel_group', type: Types::STRING),
        Groups(['fuel:get'])
    ]
    private string $fuelGroup;

    #[
        ORM\Column(name: 'fuel_type', type: Types::STRING),
        Groups(['fuel:get'])
    ]
    private string $fuelType;

    #[
        ORM\Column(name: 'type_of_energy_source', type: Types::STRING),
        Groups(['fuel:get'])
    ]
    private string $typeOfEnergySource;

    #[
        ORM\Column(name: 'sub_sector', type: 'string', length: 255, nullable: true, enumType: SubSector::class),
        Groups(['fuel:get'])
    ]
    private ?SubSector $subSector = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFuelGroup(): string
    {
        return $this->fuelGroup;
    }

    public function setFuelGroup(string $fuelGroup): self
    {
        $this->fuelGroup = $fuelGroup;

        return $this;
    }

    public function getFuelType(): string
    {
        return $this->fuelType;
    }

    public function setFuelType(string $fuelType): self
    {
        $this->fuelType = $fuelType;

        return $this;
    }

    public function getTypeOfEnergySource(): string
    {
        return $this->typeOfEnergySource;
    }

    public function setTypeOfEnergySource(string $typeOfEnergySource): self
    {
        $this->typeOfEnergySource = $typeOfEnergySource;

        return $this;
    }

    public function getSubSector(): ?SubSector
    {
        return $this->subSector;
    }

    public function setSubSector(?SubSector $subSector): self
    {
        $this->subSector = $subSector;

        return $this;
    }
}
