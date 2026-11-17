<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Application\AddDebt;
final class AddDebtCommand
{
    /** @var string */      public $storeId;
    /** @var string */      public $clientId;
    /** @var int */         public $amount;
    /** @var string|null */ public $saleId;
    /** @var string|null */ public $notes;
    public function __construct(string $storeId, string $clientId, int $amount, ?string $saleId, ?string $notes)
    { $this->storeId=$storeId; $this->clientId=$clientId; $this->amount=$amount; $this->saleId=$saleId; $this->notes=$notes; }
}
