<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Application\DeleteExpense;

final class DeleteExpenseCommand
{
    /** @var string */ public $storeId;
    /** @var string */ public $expenseId;

    public function __construct(string $storeId, string $expenseId)
    {
        $this->storeId   = $storeId;
        $this->expenseId = $expenseId;
    }
}
