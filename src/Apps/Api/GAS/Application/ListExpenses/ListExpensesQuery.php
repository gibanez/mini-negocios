<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Application\ListExpenses;

final class ListExpensesQuery
{
    /** @var string */      public $storeId;
    /** @var string|null */ public $dateFrom;
    /** @var string|null */ public $dateTo;
    /** @var string|null */ public $category;

    public function __construct(string $storeId, ?string $dateFrom, ?string $dateTo, ?string $category)
    {
        $this->storeId  = $storeId;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
        $this->category = $category;
    }
}
