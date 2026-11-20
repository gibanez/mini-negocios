<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Application\DeleteExpense;

use Gaby\Apps\Api\GAS\Domain\Exception\ExpenseNotFoundException;
use Gaby\Apps\Api\GAS\Domain\ExpenseRepositoryInterface;

final class DeleteExpenseHandler
{
    /** @var ExpenseRepositoryInterface */ private $repository;

    public function __construct(ExpenseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function handle(DeleteExpenseCommand $command): void
    {
        $expense = $this->repository->findById($command->expenseId);
        if ($expense === null || $expense->storeId() !== $command->storeId) {
            throw ExpenseNotFoundException::withId($command->expenseId);
        }
        $this->repository->delete($command->expenseId);
    }
}
