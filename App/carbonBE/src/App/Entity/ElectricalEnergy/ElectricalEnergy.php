<?php

namespace App\Entity\ElectricalEnergy;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Factor\Factor;
use App\Enum\SubSector;
use App\Filters\BaseFilters;
use App\Repository\ElectricalEnergy\ElectricalEnergyRepository;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ORM\Table(name: 'electrical_energies'),
    ORM\Entity(repositoryClass: ElectricalEnergyRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/electrical-energies',
            openapiContext: [
                'parameters' => BaseFilters::LIST,
            ],
            normalizationContext: [
                'groups' => ['electrical_energy:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_electrical_energies_index'
        ),
        new Get(
            uriTemplate: '/electrical-energies/{id}',
            normalizationContext: [
                'groups' => ['electrical_energy:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_electrical_energies_get'
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_ADMIN")',
)]
class ElectricalEnergy extends Factor implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['electrical_energy:get']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(name: 'energy_type', type: Types::STRING),
        Groups(['electrical_energy:get'])
    ]
    private string $energyType;

    #[
        ORM\Column(name: 'year', type: Types::STRING, nullable: true),
        Groups(['electrical_energy:get'])
    ]
    private ?string $year = null;

    #[
        ORM\Column(name: 'power_plant_type', type: Types::STRING, nullable: true),
        Groups(['electrical_energy:get'])
    ]
    private ?string $powerPlantType = null;

    #[
        ORM\Column(name: 'sub_sector', type: 'string', length: 255, nullable: true, enumType: SubSector::class),
        Groups(['electrical_energy:get'])
    ]
    private ?SubSector $subSector = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnergyType(): string
    {
        return $this->energyType;
    }

    public function setEnergyType(string $energyType): self
    {
        $this->energyType = $energyType;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getPowerPlantType(): ?string
    {
        return $this->powerPlantType;
    }

    public function setPowerPlantType(?string $powerPlantType): self
    {
        $this->powerPlantType = $powerPlantType;

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
