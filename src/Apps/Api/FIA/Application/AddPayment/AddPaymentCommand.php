<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Application\AddPayment;
final class AddPaymentCommand
{
    /** @var string */      public $storeId;
    /** @var string */      public $clientId;
    /** @var int */         public $amount;
    /** @var string */      public $paymentMethod;
    /** @var string|null */ public $notes;
    public function __construct(string $storeId, string $clientId, int $amount, string $paymentMethod, ?string $notes)
    { $this->storeId=$storeId; $this->clientId=$clientId; $this->amount=$amount; $this->paymentMethod=$paymentMethod; $this->notes=$notes; }
}
