<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Application\CreateExpense;

use Gaby\Apps\Api\GAS\Domain\Expense;
use Gaby\Apps\Api\GAS\Domain\ExpenseRepositoryInterface;

final class CreateExpenseHandler
{
    /** @var ExpenseRepositoryInterface */ private $repository;

    public function __construct(ExpenseRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function handle(CreateExpenseCommand $command): Expense
    {
        $expense = Expense::create(
            $command->storeId,
            $command->amount,
            $command->category,
            $command->recipient,
            $command->description,
            $command->paymentMethod,
            $command->responsibleId,
            $command->responsibleName,
            $command->date
        );
        $this->repository->save($expense);
        return $expense;
    }
}
