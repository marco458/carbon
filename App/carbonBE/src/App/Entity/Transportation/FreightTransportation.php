<?php

namespace App\Entity\Transportation;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Factor\Factor;
use App\Filters\BaseFilters;
use App\Repository\Transportation\FreightTransportationRepository;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ORM\Table(name: 'freight_transportations'),
    ORM\Entity(repositoryClass: FreightTransportationRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/freight-transportations',
            openapiContext: [
                'parameters' => BaseFilters::LIST,
            ],
            normalizationContext: [
                'groups' => ['freight_transportation:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_freight_transportations_index'
        ),
        new Get(
            uriTemplate: '/freight-transportations/{id}',
            normalizationContext: [
                'groups' => ['freight_transportation:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_freight_transportations_get'
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_ADMIN")',
)]
class FreightTransportation extends Factor implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['freight_transportation:get']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(name: 'vehicle_type', type: Types::STRING),
        Groups(['freight_transportation:get'])
    ]
    private string $vehicleType;

    #[
        ORM\Column(name: 'fuel_and_load', type: Types::STRING),
        Groups(['freight_transportation:get'])
    ]
    private string $fuelAndLoad;

    #[
        ORM\Column(name: 'euro_standard', type: Types::STRING),
        Groups(['freight_transportation:get'])
    ]
    private string $euroStandard;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVehicleType(): string
    {
        return $this->vehicleType;
    }

    public function setVehicleType(string $vehicleType): self
    {
        $this->vehicleType = $vehicleType;

        return $this;
    }

    public function getFuelAndLoad(): string
    {
        return $this->fuelAndLoad;
    }

    public function setFuelAndLoad(string $fuelAndLoad): self
    {
        $this->fuelAndLoad = $fuelAndLoad;

        return $this;
    }

    public function getEuroStandard(): string
    {
        return $this->euroStandard;
    }

    public function setEuroStandard(string $euroStandard): self
    {
        $this->euroStandard = $euroStandard;

        return $this;
    }
}
