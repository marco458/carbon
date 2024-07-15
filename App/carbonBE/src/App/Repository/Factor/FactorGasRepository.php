<?php

namespace App\Repository\Factor;

use App\Entity\Factor\FactorGas;
use Core\Repository\BaseRepository;

class FactorGasRepository extends BaseRepository
{
    public const ENTITY_CLASS_NAME = FactorGas::class;

    public function filterByGasActivity(string $factorFqcn, int $factorId, ?string $activity = null): array
    {
        $qb = $this->createQueryBuilder('fg')
            ->leftJoin('fg.gas', 'g')
            ->andWhere('fg.factorFqcn = :factorFqcn')
            ->andWhere('fg.factorId = :factorId')
            ->setParameter('factorFqcn', $factorFqcn)
            ->setParameter('factorId', $factorId);

        if (is_null($activity)) {
            $qb->andWhere('g.activity IS NULL');
        } else {
            $qb->andWhere('g.activity LIKE :activity')
                ->setParameter('activity', $activity);
        }

        return $qb->getQuery()->getResult();
    }
}
