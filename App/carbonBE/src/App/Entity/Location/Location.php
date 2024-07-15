<?php

namespace App\Entity\Location;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\Location\LocationLevel1;
use App\Enum\Location\LocationLevel2;
use App\Filters\LocationFilters;
use App\Repository\Location\LocationRepository;
use App\State\Location\LocationCollectionStateProvider;
use App\State\Location\LocationStateProcessor;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Core\Entity\User\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ORM\Table(name: 'locations'),
    ORM\Entity(repositoryClass: LocationRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/locations',
            openapiContext: [
                'parameters' => LocationFilters::LIST,
            ],
            normalizationContext: [
                'groups' => ['location:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_locations_index',
            provider: LocationCollectionStateProvider::class
        ),
        new Post(
            uriTemplate: '/locations',
            normalizationContext: [
                'groups' => ['location:get'],
            ],
            denormalizationContext: [
                'groups' => ['location:create'],
            ],
            name: 'api_v1_locations_create',
            processor: LocationStateProcessor::class
        ),
        new Get(
            uriTemplate: '/locations/{id}',
            normalizationContext: [
                'groups' => ['location:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_locations_get'
        ),
        new Patch(
            uriTemplate: '/locations/{id}',
            normalizationContext: [
                'groups' => ['location:get'],
            ],
            denormalizationContext: [
                'groups' => ['location:update'],
            ],
            name: 'api_v1_locations_update'
        ),
        new Delete(
            uriTemplate: '/locations/{id}',
            name: 'api_v1_locations_delete',
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_ADMIN")',
)]
class Location implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['location:get']),
    ]
    private ?int $id = null;

    #[
        ORM\ManyToOne(targetEntity: User::class, cascade: ['persist']),
        ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE'),
        Groups(['location:user', 'location:get']),
    ]
    private User $user;

    #[
        ORM\Column(name: 'name', type: 'string', length: 255),
        Groups(['location:create', 'location:update', 'location:get'])
    ]
    private string $name;

    #[
        ORM\Column(name: 'level1', type: 'string', length: 255, nullable: true, enumType: LocationLevel1::class),
        Groups(['location:create', 'location:update', 'location:get'])
    ]
    private ?LocationLevel1 $level1 = null;

    #[
        ORM\Column(name: 'level2', type: 'string', length: 255, nullable: true, enumType: LocationLevel2::class),
        Groups(['location:create', 'location:update', 'location:get'])
    ]
    private ?LocationLevel2 $level2 = null;

    #[
        ORM\Column(name: 'description', type: 'string', length: 255),
        Groups(['location:create', 'location:update', 'location:get'])
    ]
    private string $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLevel1(): ?LocationLevel1
    {
        return $this->level1;
    }

    public function setLevel1(?LocationLevel1 $level1): self
    {
        $this->level1 = $level1;

        return $this;
    }

    public function getLevel2(): ?LocationLevel2
    {
        return $this->level2;
    }

    public function setLevel2(?LocationLevel2 $level2): self
    {
        $this->level2 = $level2;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
