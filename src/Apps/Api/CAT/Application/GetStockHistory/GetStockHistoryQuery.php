<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\GetStockHistory;
final class GetStockHistoryQuery
{
    /** @var string */ public $storeId;
    /** @var string */ public $productId;
    public function __construct(string $storeId, string $productId) { $this->storeId=$storeId; $this->productId=$productId; }
}
