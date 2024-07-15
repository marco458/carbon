<?php

namespace App\Repository\Location;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\Operation;
use App\Entity\Location\Location;
use Core\Entity\User\User;
use Core\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class LocationRepository extends BaseRepository
{
    public const ENTITY_CLASS_NAME = Location::class;

    public function getFilteredCollection(
        iterable $apiPlatformExtensions,
        array $context,
        Operation $operation
    ): iterable {
        $qb = $this->createQueryBuilder('l');
        $qb->leftJoin(User::class, 'u', Join::WITH, 'u.id = l.user');

        $filters = $context['filters'] ?? null;

        $itemsPerPage = $filters['items_per_page'] ?? 10;
        $page = $filters['page'] ?? 1;

        if (null !== $filters) {
            if (isset($filters['user_id'])) {
                $qb->andWhere('l.user = :user')
                    ->setParameter('user', (int) $filters['user_id']);
            }
            if (isset($filters['level1'])) {
                $qb->andWhere('l.level1 = :level1')
                    ->setParameter('level1', $filters['level1']);
            }
            if (isset($filters['level2'])) {
                $qb->andWhere('l.level2 = :level2')
                    ->setParameter('level2', $filters['level2']);
            }
            if (isset($filters['name'])) {
                $qb->andWhere('l.name LIKE :name')
                    ->setParameter('name', $filters['name']);
            }
        }

        $qb->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);

        $dp = new DoctrinePaginator($qb);

        return new Paginator($dp);
    }
}
