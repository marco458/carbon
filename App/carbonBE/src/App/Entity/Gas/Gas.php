<?php

namespace App\Entity\Gas;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\GasActivity;
use App\Filters\GasFilters;
use App\Repository\Gas\GasRepository;
use App\State\GasCollectionStateProvider;
use App\State\GasStateProcessor;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Table(name: 'gases'),
    ORM\Entity(repositoryClass: GasRepository::class),
    ORM\HasLifecycleCallbacks()
]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/gases',
            openapiContext: [
                'parameters' => GasFilters::LIST,
            ],
            normalizationContext: [
                'groups' => ['gas:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_gases_index',
            provider: GasCollectionStateProvider::class
        ),
        new Post(
            uriTemplate: '/gases',
            normalizationContext: [
                'groups' => ['gas:get'],
            ],
            denormalizationContext: [
                'groups' => ['gas:create'],
            ],
            name: 'api_v1_gases_create',
            processor: GasStateProcessor::class
        ),
        new Get(
            uriTemplate: '/gases/{id}',
            normalizationContext: [
                'groups' => ['gas:get', 'factor:get', 'unit:get'],
            ],
            name: 'api_v1_gases_get'
        ),
        new Patch(
            uriTemplate: '/gases/{id}',
            normalizationContext: [
                'groups' => ['gas:get'],
            ],
            denormalizationContext: [
                'groups' => ['gas:update'],
            ],
            name: 'api_v1_gases_update'
        ),
        new Delete(
            uriTemplate: '/gases/{id}',
            name: 'api_v1_gases_delete',
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_ADMIN")',
)]
class Gas implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['gas:get']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(name: 'name', type: 'string', length: 255),
        Assert\NotBlank(message: 'gas.validation.name.please_enter_name', groups: ['gas:create', 'gas:update']),
        Assert\Length(
            min: 1,
            max: 255,
            minMessage: 'gas.validation.name.must_be_at_least_1_character_long',
            maxMessage: 'gas.validation.name.must_be_under_255_characters',
            groups: ['gas:create', 'gas:update']
        ),
        Groups(['gas:create', 'gas:update', 'gas:get'])
    ]
    private string $name;

    #[
        ORM\Column(name: 'formula', type: Types::STRING),
        Assert\Type(type: Types::STRING, groups: ['gas:create', 'gas:update']),
        Groups(['gas:create', 'gas:update', 'gas:get'])
    ]
    private string $formula;

    #[
        ORM\Column(name: 'activity', type: 'string', length: 255, nullable: true, enumType: GasActivity::class),
        Groups(['gas:create', 'gas:update', 'gas:get'])
    ]
    private ?GasActivity $activity = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFormula(): string
    {
        return $this->formula;
    }

    public function setFormula(string $formula): self
    {
        $this->formula = $formula;

        return $this;
    }

    public function getActivity(): ?GasActivity
    {
        return $this->activity;
    }

    public function setActivity(?GasActivity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }
}
