<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Application\CreateSale;

final class CreateSaleCommand
{
    /** @var string */      public $storeId;
    /** @var string|null */ public $clientId;
    /** @var array<int,array<string,mixed>> */ public $items;
    /** @var string */      public $paymentMethod;
    /** @var string|null */ public $notes;
    /** @var string */      public $responsibleId;
    /** @var string */      public $responsibleName;

    public function __construct(string $storeId, ?string $clientId, array $items,
        string $paymentMethod, ?string $notes, string $responsibleId, string $responsibleName)
    {
        $this->storeId         = $storeId;
        $this->clientId        = $clientId;
        $this->items           = $items;
        $this->paymentMethod   = $paymentMethod;
        $this->notes           = $notes;
        $this->responsibleId   = $responsibleId;
        $this->responsibleName = $responsibleName;
    }
}
