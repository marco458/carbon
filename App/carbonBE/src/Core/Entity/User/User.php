<?php

declare(strict_types=1);

namespace Core\Entity\User;

use ApiPlatform\Doctrine\Common\Filter\OrderFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Core\Constant\Constants;
use Core\Constant\UserRoles;
use Core\Dto\Authentication\RegisterUserRequest;
use Core\Dto\Authentication\RequestPasswordReset;
use Core\Entity\EntityInterface;
use Core\Filter\QuerySearchFilter;
use Core\Repository\UserRepository;
use Core\State\User\UserActivate;
use Core\State\User\UserDeleteStateProcessor;
use Core\State\User\UserItemStateProvider;
use Core\State\User\UserRegisterStateProcessor;
use Core\State\User\UserRequestResetPasswordStateProcessor;
use Core\State\User\UserResetPasswordStateProcessor;
use Core\State\User\UserStateProcessor;
use Core\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: UserRepository::class),
    UniqueEntity(fields: ['email'], groups: ['user:create', 'register', 'user:import']),
    ORM\HasLifecycleCallbacks(),
    ORM\Table(name: 'users'),
]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/users/{id}',
            requirements: [
                'id' => '\d+',
            ],
            normalizationContext: [
                'groups' => ['user:get', 'list', 'user:profile_picture', 'file:get'],
            ],
            security: 'is_granted("ROLE_ADMIN")',
            name: 'api_v1_users_get',
            provider: UserItemStateProvider::class,
        ),
        new Post(
            uriTemplate: '/users',
            normalizationContext: [
                'groups' => ['user:get', 'common:read'],
            ],
            denormalizationContext: [
                'groups' => ['user:create', 'user:password'],
            ],
            security: 'is_granted("create_user", request)',
            validationContext: [
                'groups' => ['user:create', 'user:password'],
            ],
            name: 'api_v1_users_create',
            processor: UserStateProcessor::class
        ),
        new Post(
            uriTemplate: '/users/register',
            normalizationContext: [
                'groups' => ['register', 'user:get'],
            ],
            denormalizationContext: [
                'groups' => ['register', 'user:register'],
            ],
            validationContext: [
                'groups' => ['register', 'user:register'],
            ],
            input: RegisterUserRequest::class,
            name: 'api_v1_users_register',
            processor: UserRegisterStateProcessor::class
        ),
        new Get(
            uriTemplate: '/users/activate',
            openapiContext: [
                'summary' => 'Activate user.',
            ],
            normalizationContext: [
                'groups' => ['token:get', 'token:user', 'user:get'],
            ],
            denormalizationContext: [
                'groups' => ['activate'],
            ],
            validationContext: [
                'groups' => ['user:register'],
            ],
            name: 'api_v1_users_activate',
            provider: UserActivate::class
        ),
        new GetCollection(
            uriTemplate: '/users',
            defaults: [
                // '_pagination_metadata' => false, //use to disable pagination metadata
            ],
            normalizationContext: [
                'groups' => ['user:get'],
            ],
            security: 'is_granted("ROLE_ADMIN")',
            name: 'api_v1_users_index',
            // provider: UserCollectionStateProvider::class
        ),
        new Get(
            uriTemplate: '/me',
            defaults: [
                'id' => 0,
            ],
            openapiContext: [
                'summary' => 'Get currently logged in user.',
                'parameters' => [],
            ],
            normalizationContext: [
                'groups' => ['user:get', 'user:tags', 'tag:get', 'user:profile_picture', 'file:get'],
            ],
            security: 'is_granted("IS_AUTHENTICATED_FULLY")',
            name: 'api_v1_me_get',
            provider: UserItemStateProvider::class,
        ),
        new Patch(
            uriTemplate: '/users/{id}',
            normalizationContext: [
                'groups' => ['user:get'],
            ],
            denormalizationContext: [
                'groups' => ['user:update', 'user:roles'],
            ],
            security: 'is_granted("modify_user", object)',
            validationContext: [
                'groups' => ['user:update'],
            ],
            name: 'api_v1_users_update',
            processor: UserStateProcessor::class
        ),
        new Delete(
            uriTemplate: '/users/{id}',
            security: 'is_granted("delete_user", object)',
            name: 'api_v1_users_delete',
            processor: UserDeleteStateProcessor::class
        ),
        new Post(
            uriTemplate: '/reset-password',
            status: 204,
            openapiContext: [
                'summary' => 'Request a password reset for given e-mail address.',
            ],
            denormalizationContext: [
                'groups' => ['password-reset:create'],
            ],
            input: RequestPasswordReset::class,
            name: 'api_v1_request_reset_password',
            processor: UserRequestResetPasswordStateProcessor::class
        ),
        new Put(
            uriTemplate: '/reset-password',
            openapiContext: [
                'summary' => 'Set a new password using the emailed secret hash.',
            ],
            normalizationContext: [
                'groups' => ['token:get', 'token:user', 'user:get'],
            ],
            denormalizationContext: [
                'groups' => ['user:password', 'user:password_reset_token'],
            ],
            validationContext: [
                'groups' => ['user:password', 'user:password_reset_token'],
            ],
            name: 'api_v1_reset_password',
            processor: UserResetPasswordStateProcessor::class,
            extraProperties: ['standard_put' => true],
        ),
    ],
    routePrefix: '/'.Constants::API_VERSION_V1,
    mercure: false,
    compositeIdentifier: true,
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id' => OrderFilterInterface::DIRECTION_ASC,
        'firstName' => OrderFilterInterface::DIRECTION_ASC,
        'lastName' => OrderFilterInterface::DIRECTION_ASC,
    ]
)]
#[
    ApiFilter(QuerySearchFilter::class, properties: ['email', 'firstName', 'lastName'])
]
class User implements EntityInterface, UserInterface, PasswordAuthenticatedUserInterface, LegacyPasswordAuthenticatedUserInterface
{
    use TimestampableTrait;

    public const DEFAULT_ROLE = UserRoles::USER;
    public const ACTIVATION_TOKEN_EXPIRATION = 1440; // in mins

    #[
        ORM\OneToMany(
            mappedBy: 'user',
            targetEntity: Token::class,
            cascade: ['persist', 'remove'],
            orphanRemoval: true
        ),
        Groups(['user:tokens'])
    ]
    protected Collection $tokens;

    #[
        ORM\Column(name: 'id', type: Types::INTEGER, nullable: false),
        ORM\GeneratedValue(strategy: 'IDENTITY'),
        ORM\Id,
        Groups(['user:get', 'user:info']),
    ]
    private ?int $id = null;

    #[
        ORM\Column(name: 'email', type: Types::STRING, length: 180, unique: true, nullable: false),
        Assert\NotBlank(
            message: 'user.validation.email.cannot_be_blank',
            groups: ['user:create', 'user:update', 'user:register', 'user:import']
        ),
        Assert\Email(
            message: 'user.validation.email.must_be_valid',
            groups: ['user:create', 'user:update', 'user:register', 'user:import']
        ),
        Groups(['user:get', 'user:create', 'user:register', 'user:import', 'user:info']),
    ]
    private string $email;

    #[
        ORM\Column(name: 'roles', type: Types::JSON),
        Groups(['user:get', 'user:create', 'user:roles'])
    ]
    private array $roles = [User::DEFAULT_ROLE];

    #[
        Assert\NotBlank(message: 'user.validation.plaintext_password.cannot_be_blank', groups: ['user:password']),
        Groups(['user:password', 'user:register']),
        SerializedName('password'),
    ]
    private string $plaintextPassword = '';

    #[

        Groups(['user:register', 'user:repeat_password']),
        SerializedName('repeat_password'),
    ]
    private string $repeatPlaintextPassword = '';

    #[
        ORM\Column(name: 'password', type: Types::STRING, length: 255, nullable: false),
        Assert\Type(Types::STRING),
        Groups(['user:current_password']),
        SerializedName('current_password'),
    ]
    private string $password = '';

    #[
        ORM\Column(name: 'first_name', type: Types::STRING, length: 255, nullable: false),
        Assert\NotBlank(message: 'user.validation.first_name.cannot_be_blank', groups: ['user:create', 'user:update', 'user:register']),
        Assert\Length(
            min: 2,
            max: 255,
            minMessage: 'user.validation.first_name.must_be_longer_than_2_characters',
            maxMessage: 'user.validation.first_name.must_be_under_255_characters',
            groups: ['user:create', 'user:update', 'user:register', 'user:import']
        ),
        Assert\Type(Types::STRING, groups: ['user:create', 'user:update', 'user:register']),
        Groups(['user:get', 'user:create', 'user:update', 'user:register', 'user:import', 'user:info'])
    ]
    private string $firstName;

    #[
        ORM\Column(name: 'last_name', type: Types::STRING, length: 255, nullable: false),
        Assert\NotBlank(message: 'user.validation.last_name.cannot_be_blank', groups: ['user:create', 'user:update', 'user:register']),
        Assert\Type(Types::STRING, groups: ['user:create', 'user:update', 'user:register']),
        Assert\Length(
            min: 2,
            max: 255,
            minMessage: 'user.validation.last_name.must_be_longer_than_2_characters',
            maxMessage: 'user.validation.last_name.must_be_under_255_characters',
            groups: ['user:create', 'user:update', 'user:register']
        ),
        Groups(['user:get', 'user:create', 'user:update', 'user:register', 'user:import', 'user:info']),
    ]
    private string $lastName;

    #[
        ORM\Column(name: 'organization_name', type: Types::STRING, length: 255, nullable: false),
        Assert\NotBlank(
            message: 'user.validation.organization_name.cannot_be_blank',
            groups: ['user:create', 'user:update', 'user:register']
        ),
        Assert\Length(
            min: 2,
            max: 255,
            minMessage: 'user.validation.organization_name.must_be_longer_than_2_characters',
            maxMessage: 'user.validation.organization_name.must_be_under_255_characters',
            groups: ['user:create', 'user:update', 'user:register']
        ),
        Assert\Type(Types::STRING, groups: ['user:create', 'user:update', 'user:register']),
        Groups(['user:get', 'user:create', 'user:update', 'user:register'])
    ]
    private string $organizationName;

    #[
        ORM\Column(name: 'active', type: Types::BOOLEAN, nullable: false, options: ['default' => 0]),
        Groups(['user:get'])
    ]
    private bool $active = false;

    #[
        ORM\Column(name: 'login_token', type: Types::STRING, nullable: true),
    ]
    private ?string $loginToken = null;

    #[
        ORM\Column(name: 'password_reset_token', type: Types::STRING, nullable: true),
        Groups(['user:password_reset_token'])
    ]
    private ?string $passwordResetToken = null;

    #[
        ORM\Column(name: 'password_reset_token_expires_at', type: Types::DATETIME_MUTABLE, nullable: true),
        Groups(['user:password_reset_token_expires_at']),
        Assert\NotBlank(message: 'user.validation.password_reset_token_expires_at.cannot_be_blank', groups: ['user:user:password_reset_token']),
    ]
    private ?\DateTimeInterface $passwordResetTokenExpiresAt = null;

    #[
        ORM\Column(name: 'activation_token', type: Types::STRING, nullable: true),
        Groups(['user:activation_token'])
    ]
    private ?string $activationToken = null;

    #[
        ORM\Column(name: 'activation_token_expires_at', type: Types::DATETIME_MUTABLE, nullable: true),
        Groups(['user:activation_token']),
        Assert\NotBlank(message: 'user.validation.activation_token_expires_at.cannot_be_blank', groups: ['user:user:activation_token']),
    ]
    private ?\DateTimeInterface $activationTokenExpiresAt = null;

    #[
        ORM\Column(name: 'email_confirmed_at', type: Types::DATETIME_MUTABLE, nullable: true),
    ]
    private ?\DateTimeInterface $emailConfirmedAt = null;

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getOrganizationName(): string
    {
        return $this->organizationName;
    }

    public function setOrganizationName(string $organizationName): self
    {
        $this->organizationName = $organizationName;

        return $this;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getLoginToken(): ?string
    {
        return $this->loginToken;
    }

    public function setLoginToken(?string $loginToken): User
    {
        $this->loginToken = $loginToken;

        return $this;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $passwordResetToken): User
    {
        $this->passwordResetToken = $passwordResetToken;

        return $this;
    }

    public function getPasswordResetTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->passwordResetTokenExpiresAt;
    }

    public function setPasswordResetTokenExpiresAt(?\DateTimeInterface $passwordResetTokenExpiresAt): User
    {
        $this->passwordResetTokenExpiresAt = $passwordResetTokenExpiresAt;

        return $this;
    }

    public function getEmailConfirmedAt(): ?\DateTimeInterface
    {
        return $this->emailConfirmedAt;
    }

    public function setEmailConfirmedAt(?\DateTimeInterface $emailConfirmedAt): User
    {
        $this->emailConfirmedAt = $emailConfirmedAt;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        // avoid adding multiple roles for now
        $this->roles = [$roles[0]];

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

    public function eraseCredentials(): void
    {
    }

    public function getFullName(): string
    {
        return trim($this->firstName.' '.$this->lastName);
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function getPlaintextPassword(): ?string
    {
        return $this->plaintextPassword;
    }

    public function setPlaintextPassword(string $plaintextPassword): User
    {
        $this->plaintextPassword = $plaintextPassword;

        return $this;
    }

    public function getRepeatPlaintextPassword(): string
    {
        return $this->repeatPlaintextPassword;
    }

    public function setRepeatPlaintextPassword(string $repeatPlaintextPassword): User
    {
        $this->repeatPlaintextPassword = $repeatPlaintextPassword;

        return $this;
    }

    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    public function setActivationToken(?string $activationToken): User
    {
        $this->activationToken = $activationToken;

        return $this;
    }

    public function getActivationTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->activationTokenExpiresAt;
    }

    public function setActivationTokenExpiresAt(?\DateTimeInterface $activationTokenExpiresAt): User
    {
        $this->activationTokenExpiresAt = $activationTokenExpiresAt;

        return $this;
    }
}
