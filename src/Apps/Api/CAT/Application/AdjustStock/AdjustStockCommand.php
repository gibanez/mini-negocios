<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\AdjustStock;
final class AdjustStockCommand
{
    /** @var string */      public $storeId;
    /** @var string */      public $productId;
    /** @var int */         public $newStock;
    /** @var string */      public $reason;
    /** @var string|null */ public $note;
    /** @var string */      public $responsibleName;
    public function __construct(string $storeId, string $productId, int $newStock,
        string $reason, ?string $note, string $responsibleName)
    {
        $this->storeId=$storeId; $this->productId=$productId; $this->newStock=$newStock;
        $this->reason=$reason; $this->note=$note; $this->responsibleName=$responsibleName;
    }
}
