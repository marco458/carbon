<?php

declare(strict_types=1);

namespace Core\Dto\Core;

use ApiPlatform\Doctrine\Orm\Paginator;

final class ControllerPaginatedResultDTO
{
    private ?Paginator $paginator = null;

    public function getPaginator(): ?Paginator
    {
        return $this->paginator;
    }

    public function setPaginator(?Paginator $paginator): void
    {
        $this->paginator = $paginator;
    }
}
