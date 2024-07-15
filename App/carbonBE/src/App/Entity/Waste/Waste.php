<?php

namespace App\Entity\Waste;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Factor\Factor;
use App\Filters\BaseFilters;
use App\Repository\Waste\WasteRepository;
use App\State\Factor\FactorStateProcessor;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Table(name: 'wastes'),
    ORM\Entity(repositoryClass: WasteRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/wastes',
            openapiContext: [
                'parameters' => BaseFilters::LIST,
            ],
            normalizationContext: [
                'groups' => ['waste:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_wastes_index'
        ),
        new Post(
            uriTemplate: '/wastes',
            normalizationContext: [
                'groups' => ['waste:get'],
            ],
            denormalizationContext: [
                'groups' => ['waste:create', 'factor:create'],
            ],
            name: 'api_v1_wastes_create',
            processor: FactorStateProcessor::class,
        ),
        new Get(
            uriTemplate: '/wastes/{id}',
            normalizationContext: [
                'groups' => ['waste:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_wastes_get'
        ),
        new Patch(
            uriTemplate: '/wastes/{id}',
            normalizationContext: [
                'groups' => ['waste:get'],
            ],
            denormalizationContext: [
                'groups' => ['waste:update'],
            ],
            name: 'api_v1_wastes_update'
        ),
        new Delete(
            uriTemplate: '/wastes/{id}',
            name: 'api_v1_wastes_delete',
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_ADMIN")',
)]
class Waste extends Factor implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['waste:get']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(name: 'category', type: Types::STRING),
        Assert\Type(type: Types::STRING, groups: ['waste:create', 'waste:update']),
        Groups(['waste:create', 'waste:update', 'waste:get'])
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
