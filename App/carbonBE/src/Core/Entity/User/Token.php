<?php

declare(strict_types=1);

namespace Core\Entity\User;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use Core\Constant\Constants;
use Core\Dto\Authentication\LoginRequest;
use Core\Entity\EntityInterface;
use Core\Repository\TokenRepository;
use Core\State\Token\TokenStateProcessor;
use Core\State\Token\TokenStateProvider;
use Core\Traits\TimestampableTrait;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: TokenRepository::class),
    ORM\Table(name: 'tokens'),
    ORM\UniqueConstraint(name: 'tokenkey_UNIQUE', columns: ['token_key']),
    ORM\HasLifecycleCallbacks(),
]
#[ApiResource(
    shortName: 'Token',
    operations: [
        new Get(
            uriTemplate: '/token/refresh/{refreshToken}',
            uriVariables: [
                'refreshToken' => new Link(toProperty: 'refreshToken', fromClass: Token::class),
            ],
            normalizationContext: [
                'groups' => ['token:get'],
            ],
            serialize: true,
            name: 'api_v1_token_refresh',
            provider: TokenStateProvider::class,
        ),
        new Post(
            uriTemplate: '/token',
            normalizationContext: [
                'groups' => ['token:get', 'token:user', 'user:get'],
            ],
            denormalizationContext: [
                'groups' => ['login'],
            ],
            input: LoginRequest::class,
            name: 'api_v1_token_issue',
            processor: TokenStateProcessor::class,
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    mercure: false,
    compositeIdentifier: true,
)]
class Token extends AbstractToken implements EntityInterface, \Stringable
{
    use TimestampableTrait;
    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['token:get']),
        ApiProperty(identifier: false)
    ]
    private ?int $id = null;
    #[
        ORM\Column(name: 'token_key', type: Types::STRING, length: 255, nullable: false),
        Assert\NotBlank(),
        Assert\NotNull(),
        Groups(['token:create', 'token:get']),
    ]
    private string $tokenKey = '';
    #[
        ORM\Column(name: 'refresh_token_key', type: Types::STRING, length: 255, nullable: false),
        Assert\NotBlank(),
        Assert\NotNull(),
        Groups(['token:create', 'token:get']),
        ApiProperty(identifier: true)
    ]
    private string $refreshTokenKey = '';
    #[
        ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER', inversedBy: 'tokens'),
        ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE'),
        Assert\Valid(),
        Groups(['token:create', 'token:user']),
    ]
    private UserInterface $user;
    #[
        ORM\Column(name: 'expires_at', type: Types::DATETIME_MUTABLE, nullable: true),
        Assert\Type(\DateTimeInterface::class),
        Groups(['token:create', 'token:get']),
    ]
    private ?\DateTimeInterface $expiresAt = null;
    #[
        ORM\Column(name: 'refresh_expires_at', type: Types::DATETIME_MUTABLE, nullable: true),
        Assert\Type(\DateTimeInterface::class),
        Groups(['token:create', 'token:get']),
    ]
    private ?\DateTimeInterface $refreshExpiresAt = null;
    #[
        ORM\Column(name: 'last_active_date', type: Types::DATETIME_MUTABLE, nullable: false),
        Assert\Type(\DateTimeInterface::class),
        Groups(['token:create', 'token:get']),
    ]
    private ?\DateTimeInterface $lastActiveDate;

    public function __construct(array $roles = [])
    {
        parent::__construct($roles);
        $this->lastActiveDate = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return (string) $this->getTokenKey();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTokenKey(): ?string
    {
        return $this->tokenKey;
    }

    public function setTokenKey(string $tokenKey): Token
    {
        $this->tokenKey = $tokenKey;

        return $this;
    }

    public function getRefreshTokenKey(): string
    {
        return $this->refreshTokenKey;
    }

    public function setRefreshTokenKey(string $refreshTokenKey): Token
    {
        $this->refreshTokenKey = $refreshTokenKey;

        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): Token
    {
        $this->user = $user;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeInterface $expiresAt): Token
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getRefreshExpiresAt(): ?\DateTimeInterface
    {
        return $this->refreshExpiresAt;
    }

    public function setRefreshExpiresAt(?\DateTimeInterface $expiresAt): Token
    {
        $this->refreshExpiresAt = $expiresAt;

        return $this;
    }

    public function getLastActiveDate(): ?\DateTimeInterface
    {
        return $this->lastActiveDate;
    }

    public function setLastActiveDate(\DateTimeInterface $lastActiveDate): Token
    {
        $this->lastActiveDate = $lastActiveDate;

        return $this;
    }

    public function getCredentials(): mixed
    {
        return null;
    }
}
