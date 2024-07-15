<?php

namespace App\Entity\Unit;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Filters\BaseFilters;
use App\Repository\Unit\UnitRepository;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Table(name: 'units'),
    ORM\Entity(repositoryClass: UnitRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/units',
            openapiContext: [
                'parameters' => BaseFilters::LIST,
            ],
            normalizationContext: [
                'groups' => ['unit:get'],
            ],
            name: 'api_v1_units_index'
        ),
        new Post(
            uriTemplate: '/units',
            normalizationContext: [
                'groups' => ['unit:get'],
            ],
            denormalizationContext: [
                'groups' => ['unit:create'],
            ],
            name: 'api_v1_units_create'
        ),
        new Get(
            uriTemplate: '/units/{id}',
            normalizationContext: [
                'groups' => ['unit:get'],
            ],
            name: 'api_v1_units_get'
        ),
        new Patch(
            uriTemplate: '/units/{id}',
            normalizationContext: [
                'groups' => ['unit:get'],
            ],
            denormalizationContext: [
                'groups' => ['unit:update'],
            ],
            name: 'api_v1_units_update'
        ),
        new Delete(
            uriTemplate: '/units/{id}',
            name: 'api_v1_units_delete',
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_ADMIN")',
)]
class Unit implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['unit:get']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(name: 'measuring_unit', type: Types::STRING),
        Assert\Type(type: Types::STRING, groups: ['unit:create', 'unit:update']),
        Groups(['unit:create', 'unit:update', 'unit:get'])
    ]
    private string $measuringUnit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMeasuringUnit(): string
    {
        return $this->measuringUnit;
    }

    public function setMeasuringUnit(string $measuringUnit): self
    {
        $this->measuringUnit = $measuringUnit;

        return $this;
    }
}
