<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Application\UpdateExpense;

final class UpdateExpenseCommand
{
    /** @var string */      public $storeId;
    /** @var string */      public $expenseId;
    /** @var int */         public $amount;
    /** @var string */      public $category;
    /** @var string */      public $recipient;
    /** @var string|null */ public $description;
    /** @var string */      public $paymentMethod;
    /** @var string */      public $date;

    public function __construct(string $storeId, string $expenseId, int $amount,
        string $category, string $recipient, ?string $description,
        string $paymentMethod, string $date)
    {
        $this->storeId       = $storeId;
        $this->expenseId     = $expenseId;
        $this->amount        = $amount;
        $this->category      = $category;
        $this->recipient     = $recipient;
        $this->description   = $description;
        $this->paymentMethod = $paymentMethod;
        $this->date          = $date;
    }
}
