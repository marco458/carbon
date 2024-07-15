<?php

declare(strict_types=1);

namespace Core\Fixtures\Factory;

use Carbon\Carbon;
use Core\Constant\UserRoles;
use Core\Entity\User\User;
use Core\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<User>
 *
 * @method static User|Proxy                     createOne(array $attributes = [])
 * @method static array<User>|array<Proxy>       createMany(int $number, array|callable $attributes = [])
 * @method static User|Proxy                     find(object|array|mixed $criteria)
 * @method static User|Proxy                     findOrCreate(array $attributes)
 * @method static User|Proxy                     first(string $sortedField = 'id')
 * @method static User|Proxy                     last(string $sortedField = 'id')
 * @method static User|Proxy                     random(array $attributes = [])
 * @method static User|Proxy                     randomOrCreate(array $attributes = [])
 * @method static array<User>|array<Proxy>       all()
 * @method static array<User>|array<Proxy>       findBy(array $attributes)
 * @method static array<User>|array<Proxy>       randomSet(int $number, array $attributes = [])
 * @method static array<User>|array<Proxy>       randomRange(int $min, int $max, array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method        User|Proxy                     ecreate(array|callable $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    public function adminUser(): self
    {
        return $this->addState(
            [
                'roles' => [UserRoles::ADMIN],
                'email' => 'admin@admin.com',
                'password' => 'admin',
                'active' => true,
                'organizationName' => 'Stellar organization',
                'createdAt' => Carbon::now()->subtract('3 months'),
                'emailConfirmedAt' => Carbon::now()->subtract('3 months'),
            ]
        );
    }

    public function normalUser(): self
    {
        return $this->addState(
            [
                'email' => 'joe@average.com',
                'firstName' => 'Joe',
                'password' => 'really?123',
                'active' => true,
                'organizationName' => 'best organization',
                'createdAt' => Carbon::now()->subtract('5 months'),
                'emailConfirmedAt' => Carbon::now()->subtract('5 months'),
            ]
        );
    }

    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'password' => self::faker()->password(),
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'active' => true,
            'organizationName' => 'best organization',
            'emailConfirmedAt' => Carbon::now(),
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->afterInstantiate(function (User $user): void {
                $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
            })
        ;
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
