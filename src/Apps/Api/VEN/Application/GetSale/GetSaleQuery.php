<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Application\GetSale;

final class GetSaleQuery
{
    /** @var string */ public $storeId;
    /** @var string */ public $saleId;

    public function __construct(string $storeId, string $saleId)
    {
        $this->storeId = $storeId;
        $this->saleId  = $saleId;
    }
}
