<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Application\ListExpenses;

use Gaby\Apps\Api\GAS\Domain\ExpenseRepositoryInterface;

final class ListExpensesHandler
{
    /** @var ExpenseRepositoryInterface */ private $repository;

    public function __construct(ExpenseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<int,array<string,mixed>> */
    public function handle(ListExpensesQuery $query): array
    {
        $expenses = $this->repository->findByStoreId(
            $query->storeId,
            $query->dateFrom,
            $query->dateTo,
            $query->category
        );
        return array_map(function ($e) { return $e->toArray(); }, $expenses);
    }
}
