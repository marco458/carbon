<?php

namespace App\Entity\Factor;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Location\Location;
use App\Enum\Consumption;
use App\Enum\GasActivity;
use App\Repository\Factor\FactorUserRepository;
use App\State\Factor\FactorUserCollectionStateProvider;
use App\State\Factor\FactorUserStateProcessor;
use Core\Constant\Constants;
use Core\Entity\EntityInterface;
use Core\Entity\User\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Table(name: 'factor_users'),
    ORM\Entity(repositoryClass: FactorUserRepository::class),
    ORM\HasLifecycleCallbacks(),
]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/factor-users',
            normalizationContext: [
                'groups' => ['factor_user:get'],
            ],
            denormalizationContext: [
                'groups' => ['factor_user:create'],
            ],
            name: 'api_v1_factor_users_create',
            processor: FactorUserStateProcessor::class
        ),
        new GetCollection(
            uriTemplate: '/factor-users',
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
                    [
                        'name' => 'user',
                        'in' => 'query',
                        'required' => false,
                        'type' => 'integer',
                        'example' => '1',
                        'default' => null,
                    ],
                ],
            ],
            normalizationContext: [
                'groups' => ['factor_user:get', 'user:get', 'item:timestamps'],
            ],
            name: 'api_v1_factor_users_index',
            provider: FactorUserCollectionStateProvider::class
        ),
        new Delete(
            uriTemplate: '/factor-users/{id}',
            requirements: ['id' => '\d+'],
            openapiContext: ['summary' => 'Delete FactorUser resource by ID'],
            name: 'api_v1_factor_users_delete',
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    security: 'is_granted("ROLE_USER")',
)]
class FactorUser implements EntityInterface
{
    #[
        ORM\Column(name: 'id', type: Types::INTEGER),
        ORM\GeneratedValue(strategy: 'AUTO'),
        ORM\Id,
        Groups(['factor_user:id', 'factor_user:get']),
        SerializedName('factor_user_id'),
    ]
    private ?int $id = null;

    #[
        ORM\ManyToOne(targetEntity: User::class, cascade: ['persist']),
        ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE'),
        Groups(['factor_user:user', 'factor_user:get']),
        ApiProperty(example: 'api/v1/users/1')
    ]
    private User $user;

    #[
        ORM\Column(name: 'factor_fqcn', type: Types::STRING),
        Assert\Type(
            Types::STRING,
            groups: ['factor_user:create']
        ),
        Assert\NotNull(
            message: 'factor_user.validation.fqcn.please_provide_fqcn',
            groups: ['factor_user:create']
        ),
        Assert\NotBlank(
            message: 'factor_user.validation.fqcn.please_provide_fqcn',
            groups: ['factor_user:create']
        ),
        Groups(['factor_user:create', 'factor_user:get']),
    ]
    private string $factorFqcn;

    #[
        ORM\Column(name: 'factor_id', type: Types::INTEGER, length: 11),
        Assert\Type(
            Types::INTEGER,
            groups: ['factor_user:create']
        ),
        Assert\NotNull(
            message: 'factor_user.validation.factor_id.please_provide_factor_id',
            groups: ['factor_user:create']
        ),
        Assert\NotBlank(
            message: 'factor_user.validation.factor_id.please_provide_factor_id',
            groups: ['factor_user:create']
        ),
        Groups(['factor_user:create', 'factor_user:get']),
    ]
    private int $factorId;

    #[
        ORM\Column(type: Types::DECIMAL, precision: 22, scale: 12),
        Assert\NotNull(
            message: 'factor_user.validation.amount.please_provide_amount',
            groups: ['factor_user:create']
        ),
        Assert\NotBlank(
            message: 'factor_user.validation.amount.please_provide_amount',
            groups: ['factor_user:create']
        ),
        Groups(['factor_user:create', 'factor_user:get']),
    ]
    private float $amount;

    #[
        ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false),
        Assert\NotNull(
            message: 'factor_user.validation.date.please_provide_date',
            groups: ['factor_user:create']
        ),
        Assert\NotBlank(
            message: 'factor_user.validation.date.please_provide_date',
            groups: ['factor_user:create']
        ),
        Groups(['factor_user:create', 'factor_user:get']),
    ]
    private \DateTimeInterface $date;

    #[
        ORM\Column(name: 'gas_activity', type: 'string', length: 255, nullable: true, enumType: GasActivity::class),
        Groups(['factor_user:create', 'factor_user:get'])
    ]
    private ?GasActivity $gasActivity = null;

    #[
        ORM\Column(name: 'consumption', type: 'string', length: 255, nullable: true, enumType: Consumption::class),
        Groups(['factor_user:create', 'factor_user:get'])
    ]
    private ?Consumption $consumption = Consumption::INDIRECT;

    #[
        ORM\Column(name: 'unit', type: Types::STRING),
        Assert\Type(
            Types::STRING,
            groups: ['factor_user:create']
        ),
        Groups(['factor_user:create', 'factor_user:get']),
    ]
    private string $unit;

    #[
        ORM\ManyToOne(targetEntity: Location::class, cascade: ['persist']),
        ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE'),
        Groups(['factor_user:location', 'factor_user:get', 'factor_user:create']),
        ApiProperty(example: 'api/v1/locations/1')
    ]
    private ?Location $location = null;

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

    public function getFactorFqcn(): string
    {
        return $this->factorFqcn;
    }

    public function setFactorFqcn(string $factorFqcn): self
    {
        $this->factorFqcn = $factorFqcn;

        return $this;
    }

    public function getFactorId(): int
    {
        return $this->factorId;
    }

    public function setFactorId(int $factorId): self
    {
        $this->factorId = $factorId;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getGasActivity(): ?GasActivity
    {
        return $this->gasActivity;
    }

    public function setGasActivity(?GasActivity $gasActivity): self
    {
        $this->gasActivity = $gasActivity;

        return $this;
    }

    public function getConsumption(): ?Consumption
    {
        return $this->consumption;
    }

    public function setConsumption(?Consumption $consumption): self
    {
        $this->consumption = $consumption;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }
}
