<?php

namespace App\Entity\Heat;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Factor\Factor;
use App\Enum\SubSector;
use App\Filters\BaseFilters;
use App\Repository\Heat\HeatRepository;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ORM\Table(name: 'heats'),
    ORM\Entity(repositoryClass: HeatRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/heats',
            openapiContext: [
                'parameters' => BaseFilters::LIST,
            ],
            normalizationContext: [
                'groups' => ['heat:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_heats_index'
        ),
        new Get(
            uriTemplate: '/heats/{id}',
            normalizationContext: [
                'groups' => ['heat:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_heats_get'
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_ADMIN")',
)]
class Heat extends Factor implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['heat:get']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(name: 'energy_type', type: Types::STRING),
        Groups(['heat:get'])
    ]
    private string $energyType;

    #[
        ORM\Column(name: 'location', type: Types::STRING, nullable: true),
        Groups(['heat:get'])
    ]
    private ?string $location = null;

    #[
        ORM\Column(name: 'technology', type: Types::STRING, nullable: true),
        Groups(['heat:get'])
    ]
    private ?string $technology = null;

    #[
        ORM\Column(name: 'sub_sector', type: 'string', length: 255, nullable: true, enumType: SubSector::class),
        Groups(['heat:get'])
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getTechnology(): ?string
    {
        return $this->technology;
    }

    public function setTechnology(?string $technology): self
    {
        $this->technology = $technology;

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
