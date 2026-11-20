<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Application\GetExpense;

final class GetExpenseQuery
{
    /** @var string */ public $storeId;
    /** @var string */ public $expenseId;

    public function __construct(string $storeId, string $expenseId)
    {
        $this->storeId   = $storeId;
        $this->expenseId = $expenseId;
    }
}
