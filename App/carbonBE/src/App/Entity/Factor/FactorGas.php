<?php

namespace App\Entity\Factor;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Gas\Gas;
use App\Repository\Factor\FactorGasRepository;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Table(name: 'factor_gases'),
    ORM\Entity(repositoryClass: FactorGasRepository::class),
    ORM\UniqueConstraint(name: 'factor_gas_UNIQUE', columns: ['factor_fqcn', 'factor_id', 'gas_id']),
    UniqueEntity(fields: ['factorFqcn', 'factorId', 'gas'], message: 'factor_gas.validation.gas_on_factor_already_exist'),
    ORM\HasLifecycleCallbacks(),
]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/factor-gases',
            normalizationContext: [
                'groups' => ['factor_gas:get'],
            ],
            denormalizationContext: [
                'groups' => ['factor_gas:create'],
            ],
            name: 'api_v1_factor_gases_create'
        ),
        new GetCollection(
            uriTemplate: '/factor-gases',
            openapiContext: [
                'parameters' => [
                    [
                        'name' => 'class_name',
                        'in' => 'query',
                        'required' => false,
                        'type' => 'string',
                        'example' => 'waste',
                        'default' => null,
                    ],
                    [
                        'name' => 'factor_id',
                        'in' => 'query',
                        'required' => false,
                        'type' => 'integer',
                        'example' => '1',
                        'default' => null,
                    ],
                ],
            ],
            normalizationContext: [
                'groups' => ['factor_gas:get', 'gas:get', 'item:timestamps'],
            ],
            name: 'api_v1_factor_gases_index',
            // provider: factorgasCollectionStateProvider::class
        ),
        new Delete(
            uriTemplate: '/factor-gases/{id}',
            requirements: ['id' => '\d+'],
            openapiContext: ['summary' => 'Delete FactorGas resource by ID'],
            name: 'api_v1_factor_gases_delete',
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_USER")',
)]
class FactorGas implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER),
        ORM\GeneratedValue(strategy: 'AUTO'),
        ORM\Id,
        Groups(['factor_gas:id', 'factor_gas:get']),
        SerializedName('factor_gas_id'),
    ]
    private ?int $id = null;

    #[
        ORM\ManyToOne(targetEntity: Gas::class, cascade: ['persist'], fetch: 'EAGER'),
        ORM\JoinColumn(name: 'gas_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE'),
        Groups(['factor_gas:gas', 'factor_gas:create', 'factor_gas:get']),
        ApiProperty(example: 'api/v1/gases/1')
    ]
    private Gas $gas;

    #[
        ORM\Column(name: 'factor_fqcn', type: Types::STRING),
        Assert\Type(
            Types::STRING,
            groups: ['factor_gas:create']
        ),
        Assert\NotNull(
            message: 'factor_gas.validation.fqcn.please_provide_fqcn',
            groups: ['factor_gas:create']
        ),
        Assert\NotBlank(
            message: 'factor_gas.validation.fqcn.please_provide_fqcn',
            groups: ['factor_gas:create']
        ),
        Groups(['factor_gas:create', 'factor_gas:get']),
    ]
    private string $factorFqcn;

    #[
        ORM\Column(name: 'factor_id', type: Types::INTEGER, length: 11),
        Assert\Type(
            Types::INTEGER,
            groups: ['factor_gas:create']
        ),
        Assert\NotNull(
            message: 'factor_gas.validation.factor_id.please_provide_factor_id',
            groups: ['factor_gas:create']
        ),
        Assert\NotBlank(
            message: 'factor_gas.validation.factor_id.please_provide_factor_id',
            groups: ['factor_gas:create']
        ),
        Groups(['factor_gas:create', 'factor_gas:get']),
    ]
    private int $factorId;

    #[
        ORM\Column(type: Types::DECIMAL, precision: 22, scale: 12, nullable: true),
        Groups(['factor_gas:create', 'factor_gas:get']),
    ]
    private ?float $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGas(): Gas
    {
        return $this->gas;
    }

    public function setGas(Gas $gas): self
    {
        $this->gas = $gas;

        return $this;
    }

    public function getfactorFqcn(): string
    {
        return $this->factorFqcn;
    }

    public function setfactorFqcn(string $factorFqcn): self
    {
        $this->factorFqcn = $factorFqcn;

        return $this;
    }

    public function getfactorId(): int
    {
        return $this->factorId;
    }

    public function setfactorId(int $factorId): self
    {
        $this->factorId = $factorId;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): self
    {
        $this->value = $value;

        return $this;
    }
}
