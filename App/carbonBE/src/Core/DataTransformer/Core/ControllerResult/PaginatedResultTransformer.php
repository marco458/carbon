<?php

declare(strict_types=1);

namespace Core\DataTransformer\Core\ControllerResult;

use ApiPlatform\Doctrine\Orm\Paginator;
use Symfony\Component\HttpFoundation\Request;

final readonly class PaginatedResultTransformer
{
    public function __construct(
        private Request $request,
        private Paginator $paginator,
    ) {
    }

    public function transform(string $controllerResult): string
    {
        $currentPage = (int) $this->paginator->getCurrentPage();
        $lastPage = (int) $this->paginator->getLastPage();

        $result = [
            'total_pages' => $this->paginator->getLastPage(),
            'current_page' => $currentPage,
            'next_page' => $currentPage === $lastPage ? null : $currentPage + 1,
            'previous_page' => 1 === $currentPage ? null : $currentPage - 1,
            'items_per_page' => $this->paginator->getItemsPerPage(),
            'total_items' => $this->paginator->getTotalItems(),
            'order' => $this->request->query->has('order') ? $this->request->query->all()['order'] : [],
            'items' => json_decode($controllerResult, true, 512, JSON_THROW_ON_ERROR),
        ];

        return json_encode($result, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}
