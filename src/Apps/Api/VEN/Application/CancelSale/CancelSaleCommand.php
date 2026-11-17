<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Application\CancelSale;

final class CancelSaleCommand
{
    /** @var string */ public $storeId;
    /** @var string */ public $saleId;
    /** @var string */ public $reason;
    /** @var string */ public $cancelledBy;
    /** @var string */ public $today;

    public function __construct(string $storeId, string $saleId, string $reason, string $cancelledBy, string $today)
    {
        $this->storeId     = $storeId;
        $this->saleId      = $saleId;
        $this->reason      = $reason;
        $this->cancelledBy = $cancelledBy;
        $this->today       = $today;
    }
}
