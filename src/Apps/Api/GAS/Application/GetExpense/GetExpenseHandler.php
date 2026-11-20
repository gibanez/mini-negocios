<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Application\GetExpense;

use Gaby\Apps\Api\GAS\Domain\Exception\ExpenseNotFoundException;
use Gaby\Apps\Api\GAS\Domain\ExpenseRepositoryInterface;

final class GetExpenseHandler
{
    /** @var ExpenseRepositoryInterface */ private $repository;

    public function __construct(ExpenseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<string,mixed> */
    public function handle(GetExpenseQuery $query): array
    {
        $expense = $this->repository->findById($query->expenseId);
        if ($expense === null || $expense->storeId() !== $query->storeId) {
            throw ExpenseNotFoundException::withId($query->expenseId);
        }
        return $expense->toArray();
    }
}
