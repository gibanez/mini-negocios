<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Application\CreateExpense;

final class CreateExpenseCommand
{
    /** @var string */      public $storeId;
    /** @var int */         public $amount;
    /** @var string */      public $category;
    /** @var string */      public $recipient;
    /** @var string|null */ public $description;
    /** @var string */      public $paymentMethod;
    /** @var string */      public $responsibleId;
    /** @var string */      public $responsibleName;
    /** @var string */      public $date;

    public function __construct(string $storeId, int $amount, string $category,
        string $recipient, ?string $description, string $paymentMethod,
        string $responsibleId, string $responsibleName, string $date)
    {
        $this->storeId         = $storeId;
        $this->amount          = $amount;
        $this->category        = $category;
        $this->recipient       = $recipient;
        $this->description     = $description;
        $this->paymentMethod   = $paymentMethod;
        $this->responsibleId   = $responsibleId;
        $this->responsibleName = $responsibleName;
        $this->date            = $date;
    }
}
