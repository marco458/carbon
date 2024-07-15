<?php

namespace App\Repository\Factor;

use ApiPlatform\Metadata\Operation;
use App\Entity\Factor\FactorUser;
use Core\Repository\BaseRepository;

class FactorUserRepository extends BaseRepository
{
    public const ENTITY_CLASS_NAME = FactorUser::class;

    public function getFilteredCollection(
        iterable $apiPlatformExtensions,
        array $context,
        Operation $operation
    ): iterable {
        $qb = $this->createQueryBuilder('fu');
        $filters = $context['filters'] ?? null;

        if (isset($filters['user'])) {
            $qb->andWhere('fu.user = :user')
                ->setParameter('user', $filters['user']);
        }

        if (isset($filters['from_date'])) {
            $qb->andWhere('fu.date > :fromDate')
                ->setParameter('fromDate', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $qb->andWhere('fu.date < :toDate')
                ->setParameter('toDate', $filters['to_date']);
        }

        if (isset($filters['location_id'])) {
            $qb->andWhere('fu.location = :location')
                ->setParameter('location', (int) $filters['location_id']);
        }

        $qb->orderBy('fu.date', 'DESC');

        return (array) $qb->getQuery()->getResult();
    }

    public function getFilteredData(array $filters): iterable
    {
        $qb = $this->createQueryBuilder('fu');

        if (isset($filters['user'])) {
            $qb->andWhere('fu.user = :user')
                ->setParameter('user', $filters['user_id']);
        }

        if (isset($filters['from_date'])) {
            $qb->andWhere('fu.date >= :fromDate')
                ->setParameter('fromDate', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $qb->andWhere('fu.date <= :toDate')
                ->setParameter('toDate', $filters['to_date']);
        }

        if (isset($filters['location_id'])) {
            $qb->andWhere('fu.location = :location')
                ->setParameter('location', (int) $filters['location_id']);
        }

        $qb->orderBy('fu.date', 'DESC');

        return (array) $qb->getQuery()->getResult();
    }
}
