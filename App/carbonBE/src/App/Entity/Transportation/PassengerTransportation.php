<?php

namespace App\Entity\Transportation;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Factor\Factor;
use App\Filters\BaseFilters;
use App\Repository\Transportation\PassengerTransportationRepository;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ORM\Table(name: 'passenger_transportations'),
    ORM\Entity(repositoryClass: PassengerTransportationRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/passenger-transportations',
            openapiContext: [
                'parameters' => BaseFilters::LIST,
            ],
            normalizationContext: [
                'groups' => ['passenger_transportation:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_passenger_transportations_index'
        ),
        new Get(
            uriTemplate: '/passenger-transportations/{id}',
            normalizationContext: [
                'groups' => ['passenger_transportation:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_passenger_transportations_get'
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_ADMIN")',
)]
class PassengerTransportation extends Factor implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['passenger_transportation:get']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(name: 'vehicle_type', type: Types::STRING),
        Groups(['passenger_transportation:get'])
    ]
    private string $vehicleType;

    #[
        ORM\Column(name: 'fuel', type: Types::STRING),
        Groups(['passenger_transportation:get'])
    ]
    private string $fuel;

    #[
        ORM\Column(name: 'vehicle_class', type: Types::STRING),
        Groups(['passenger_transportation:get'])
    ]
    private string $vehicleClass;

    #[
        ORM\Column(name: 'euro_standard', type: Types::STRING),
        Groups(['passenger_transportation:get'])
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

    public function getFuel(): string
    {
        return $this->fuel;
    }

    public function setFuel(string $fuel): self
    {
        $this->fuel = $fuel;

        return $this;
    }

    public function getVehicleClass(): string
    {
        return $this->vehicleClass;
    }

    public function setVehicleClass(string $vehicleClass): self
    {
        $this->vehicleClass = $vehicleClass;

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
