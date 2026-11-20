<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Application\UpdateExpense;

use Gaby\Apps\Api\GAS\Domain\Exception\ExpenseNotFoundException;
use Gaby\Apps\Api\GAS\Domain\ExpenseRepositoryInterface;

final class UpdateExpenseHandler
{
    /** @var ExpenseRepositoryInterface */ private $repository;

    public function __construct(ExpenseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function handle(UpdateExpenseCommand $command): void
    {
        $expense = $this->repository->findById($command->expenseId);
        if ($expense === null || $expense->storeId() !== $command->storeId) {
            throw ExpenseNotFoundException::withId($command->expenseId);
        }
        $expense->updateDetails(
            $command->amount,
            $command->category,
            $command->recipient,
            $command->description,
            $command->paymentMethod,
            $command->date
        );
        $this->repository->save($expense);
    }
}
