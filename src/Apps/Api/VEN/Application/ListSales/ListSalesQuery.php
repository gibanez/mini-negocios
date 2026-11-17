<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Application\ListSales;

final class ListSalesQuery
{
    /** @var string */      public $storeId;
    /** @var string|null */ public $dateFrom;
    /** @var string|null */ public $dateTo;
    /** @var string|null */ public $clientId;

    public function __construct(string $storeId, ?string $dateFrom = null, ?string $dateTo = null, ?string $clientId = null)
    {
        $this->storeId  = $storeId;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
        $this->clientId = $clientId;
    }
}
