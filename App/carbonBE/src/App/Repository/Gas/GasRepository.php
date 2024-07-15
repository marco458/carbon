<?php

namespace App\Repository\Gas;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\Operation;
use App\Entity\Gas\Gas;
use Core\Repository\BaseRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class GasRepository extends BaseRepository
{
    public const ENTITY_CLASS_NAME = Gas::class;

    public function getFilteredCollection(
        iterable $apiPlatformExtensions,
        array $context,
        Operation $operation
    ): iterable {
        $qb = $this->createQueryBuilder('ca');
        $filters = $context['filters'] ?? null;

        $itemsPerPage = $filters['items_per_page'] ?? 10;
        $page = $filters['page'] ?? 1;

        $qb->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);

        $dp = new DoctrinePaginator($qb);

        return new Paginator($dp);
    }
}
