<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Application\CancelPayment;
final class CancelPaymentCommand
{
    /** @var string */ public $storeId;
    /** @var string */ public $movementId;
    /** @var string */ public $reason;
    /** @var string */ public $cancelledBy;
    public function __construct(string $storeId, string $movementId, string $reason, string $cancelledBy)
    { $this->storeId=$storeId; $this->movementId=$movementId; $this->reason=$reason; $this->cancelledBy=$cancelledBy; }
}
