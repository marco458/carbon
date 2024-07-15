<?php

declare(strict_types=1);

namespace Core\Repository;

use ApiPlatform\Metadata\Operation;
use Carbon\Carbon;
use Core\Entity\User\User;
use Core\Exception\ApiNotFoundException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserRepository extends BaseRepository implements UserLoaderInterface
{
    public const ENTITY_CLASS_NAME = User::class;

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        /* @phpstan-ignore-next-line */
        return $this->findOneBy(['email' => $identifier]);
    }

    public function findOneByPasswordResetToken(?string $token): ?User
    {
        if ('' === $token) {
            return null;
        }

        /** @var User|null $user */
        $user = $this->findOneBy([
            'passwordResetToken' => $token,
        ]);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found.');
        }

        if (!$user->isActive() && (bool) $user->getEmailConfirmedAt()) {
            throw new NotFoundHttpException('This email is not allowed.');
        }

        return $user;
    }

    public function getUsersPaginated(int $pageCount): array
    {
        $query = $this->createQueryBuilder('u')
            ->orderBy('u.id', 'ASC');

        $paginator = new Paginator($query);
        $totalUsers = $paginator->count();

        $query = $paginator->getQuery()
            ->setFirstResult($pageCount * 20)
            ->setMaxResults(20);

        return [$totalUsers, $query];
    }

    /**
     * @throws Exception
     */
    public function countActiveUsersPerMonth(): array
    {
        $connection = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT
                count(id), 
                DATE_FORMAT(created_at, "%Y-%m")
            FROM 
                users 
            WHERE
                created_at > NOW() - INTERVAL 1 YEAR
            GROUP BY
                DATE_FORMAT(created_at, "%Y-%m")';

        $statement = $connection->prepare($sql);
        $result = $statement->executeQuery();

        $mappedData = [];

        foreach ($result->fetchAllNumeric() as $item) {
            $mappedData[$item[1]] = $item[0];
        }

        return $mappedData;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countActiveUsersLastNDays(int $numberOfDays): int
    {
        $nDaysAgo = Carbon::now()->subDays($numberOfDays);

        $qb = $this->createQueryBuilder('o');
        $qb
            ->select('count(o.id)')
            ->where('o.createdAt > :nDaysAgo')
            ->setParameter('nDaysAgo', $nDaysAgo);

        $result = $qb->getQuery()->getSingleScalarResult();

        if (!is_string($result) && !is_int($result)) {
            throw new NoResultException();
        }

        return (int) $result;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findUserByActivationToken(string $activationToken): User
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->where('u.activationToken = :activationToken')
            ->andWhere('u.active = 0')
            ->setParameter('activationToken', $activationToken);

        /** @var User|null $user */
        $user = $qb->getQuery()->getOneOrNullResult();

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User with given token not found.');
        }

        if ($user->getActivationTokenExpiresAt() < Carbon::now()) {
            throw new NotFoundHttpException('Token expired, to activate account reset password.');
        }

        return $user;
    }

    /**
     * @throws ApiNotFoundException
     * @throws NonUniqueResultException
     */
    public function findUserPasswordReset(string $email): User
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->where('u.active = 1 and u.email = :email')
            ->orWhere('u.active = 0 and u.emailConfirmedAt is NULL and u.email = :email')
            ->setParameter('email', $email);

        /** @var User|null $user */
        $user = $qb->getQuery()->getOneOrNullResult();

        if (!$user instanceof User) {
            throw new ApiNotFoundException('User with given email not found.');
        }

        return $user;
    }

    public function findAllByRole(
        string $role,
        iterable $apiPlatformExtensions,
        array $context,
        Operation $operation,
    ): iterable {
        $qb = $this->createQueryBuilder('o'); // alias needs to be 'o' if using api platform extensions
        $qb->andWhere('o.roles LIKE :role')
            ->setParameter('role', '%'.$role.'%');

        return $this->applyApiPlatformExtensionsToCollection($apiPlatformExtensions, $qb, $context, $operation);
    }
}
