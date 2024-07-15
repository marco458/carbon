<?php

namespace App\Entity\LandConversion;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Factor\Factor;
use App\Filters\BaseFilters;
use App\Repository\LandConversion\LandConversionRepository;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ORM\Table(name: 'land_conversions'),
    ORM\Entity(repositoryClass: LandConversionRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/land-conversions',
            openapiContext: [
                'parameters' => BaseFilters::LIST,
            ],
            normalizationContext: [
                'groups' => ['land_conversion:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_land_conversions_index'
        ),
        new Get(
            uriTemplate: '/land-conversions/{id}',
            normalizationContext: [
                'groups' => ['land_conversion:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_land_conversions_get'
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_ADMIN")',
)]
class LandConversion extends Factor implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['land_conversion:get']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(name: 'category', type: Types::STRING),
        Groups(['land_conversion:get'])
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
