<?php

namespace App\Entity\Sector;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\SectorName;
use App\Filters\BaseFilters;
use App\Repository\Sector\SectorRepository;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ORM\Table(name: 'sectors'),
    ORM\Entity(repositoryClass: SectorRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/sectors',
            openapiContext: [
                'parameters' => BaseFilters::LIST,
            ],
            normalizationContext: [
                'groups' => ['sector:get'],
            ],
            name: 'api_v1_sectors_index'
        ),
        new Post(
            uriTemplate: '/sectors',
            normalizationContext: [
                'groups' => ['sector:get'],
            ],
            denormalizationContext: [
                'groups' => ['sector:create'],
            ],
            name: 'api_v1_sectors_create'
        ),
        new Get(
            uriTemplate: '/sectors/{id}',
            normalizationContext: [
                'groups' => ['sector:get'],
            ],
            name: 'api_v1_sectors_get'
        ),
        new Patch(
            uriTemplate: '/sectors/{id}',
            normalizationContext: [
                'groups' => ['sector:get'],
            ],
            denormalizationContext: [
                'groups' => ['sector:update'],
            ],
            name: 'api_v1_sectors_update'
        ),
        new Delete(
            uriTemplate: '/sectors/{id}',
            name: 'api_v1_sectors_delete',
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_ADMIN")',
)]
class Sector implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['sector:get']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(name: 'name', type: 'string', length: 255, nullable: false, enumType: SectorName::class),
        Groups(['sector:create', 'sector:update', 'sector:get'])
    ]
    private SectorName $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): SectorName
    {
        return $this->name;
    }

    public function setName(SectorName $name): self
    {
        $this->name = $name;

        return $this;
    }
}
