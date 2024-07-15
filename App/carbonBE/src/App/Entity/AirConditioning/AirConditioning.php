<?php

namespace App\Entity\AirConditioning;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Factor\Factor;
use App\Filters\BaseFilters;
use App\Repository\AirConditioning\AirConditioningRepository;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ORM\Table(name: 'air_conditionings'),
    ORM\Entity(repositoryClass: AirConditioningRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/air-conditionings',
            openapiContext: [
                'parameters' => BaseFilters::LIST,
            ],
            normalizationContext: [
                'groups' => ['air_conditioning:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_air_conditionings_index'
        ),
        new Get(
            uriTemplate: '/air-conditionings/{id}',
            normalizationContext: [
                'groups' => ['air_conditioning:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_air_conditionings_get'
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_ADMIN")',
)]
class AirConditioning extends Factor implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['air_conditioning:get']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(name: 'category', type: Types::STRING),
        Groups(['air_conditioning:get'])
    ]
    private string $category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }
}
