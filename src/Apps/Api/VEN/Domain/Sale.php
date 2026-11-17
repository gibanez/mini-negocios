<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Domain;

use Ramsey\Uuid\Uuid;

final class Sale
{
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_CANCELLED  = 'cancelled';

    public const PAYMENT_EFECTIVO      = 'efectivo';
    public const PAYMENT_TRANSFERENCIA = 'transferencia';
    public const PAYMENT_DEBITO        = 'debito';
    public const PAYMENT_CREDITO       = 'credito';

    /** @var string[] */
    public static $validPaymentMethods = ['efectivo', 'transferencia', 'debito', 'credito', 'fiado'];

    /** @var string */      private $id;
    /** @var string */      private $storeId;
    /** @var string|null */ private $clientId;
    /** @var int */         private $total;
    /** @var string */      private $paymentMethod;
    /** @var string */      private $responsibleId;
    /** @var string */      private $responsibleName;
    /** @var string|null */ private $notes;
    /** @var string */      private $status;
    /** @var string|null */ private $cancelledAt;
    /** @var string|null */ private $cancelReason;
    /** @var string|null */ private $cancelledBy;
    /** @var string */      private $createdAt;
    /** @var SaleItem[] */  private $items = [];

    private function __construct(string $id, string $storeId, ?string $clientId,
        int $total, string $paymentMethod, string $responsibleId, string $responsibleName,
        ?string $notes, string $status, ?string $cancelledAt, ?string $cancelReason,
        ?string $cancelledBy, string $createdAt)
    {
        $this->id=$id; $this->storeId=$storeId; $this->clientId=$clientId;
        $this->total=$total; $this->paymentMethod=$paymentMethod;
        $this->responsibleId=$responsibleId; $this->responsibleName=$responsibleName;
        $this->notes=$notes; $this->status=$status; $this->cancelledAt=$cancelledAt;
        $this->cancelReason=$cancelReason; $this->cancelledBy=$cancelledBy;
        $this->createdAt=$createdAt;
    }

    public static function create(string $storeId, ?string $clientId, int $total,
        string $paymentMethod, string $responsibleId, string $responsibleName, ?string $notes): self
    {
        return new self(Uuid::uuid4()->toString(), $storeId, $clientId, $total,
            $paymentMethod, $responsibleId, $responsibleName, $notes,
            self::STATUS_COMPLETED, null, null, null, date('Y-m-d H:i:s'));
    }

    public static function fromPersistence(string $id, string $storeId, ?string $clientId,
        int $total, string $paymentMethod, string $responsibleId, string $responsibleName,
        ?string $notes, string $status, ?string $cancelledAt, ?string $cancelReason,
        ?string $cancelledBy, string $createdAt): self
    {
        return new self($id, $storeId, $clientId, $total, $paymentMethod,
            $responsibleId, $responsibleName, $notes, $status,
            $cancelledAt, $cancelReason, $cancelledBy, $createdAt);
    }

    public function id(): string              { return $this->id; }
    public function storeId(): string         { return $this->storeId; }
    public function clientId(): ?string       { return $this->clientId; }
    public function total(): int              { return $this->total; }
    public function paymentMethod(): string   { return $this->paymentMethod; }
    public function responsibleId(): string   { return $this->responsibleId; }
    public function responsibleName(): string { return $this->responsibleName; }
    public function notes(): ?string          { return $this->notes; }
    public function status(): string          { return $this->status; }
    public function cancelledAt(): ?string    { return $this->cancelledAt; }
    public function cancelReason(): ?string   { return $this->cancelReason; }
    public function cancelledBy(): ?string    { return $this->cancelledBy; }
    public function createdAt(): string       { return $this->createdAt; }
    public function isCompleted(): bool       { return $this->status === self::STATUS_COMPLETED; }

    /** @return SaleItem[] */
    public function items(): array { return $this->items; }

    /** @param SaleItem[] $items */
    public function setItems(array $items): void { $this->items = $items; }

    public function cancel(string $reason, string $cancelledBy): void
    {
        $this->status       = self::STATUS_CANCELLED;
        $this->cancelReason = $reason;
        $this->cancelledBy  = $cancelledBy;
        $this->cancelledAt  = date('Y-m-d H:i:s');
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'clientId'        => $this->clientId,
            'total'           => $this->total,
            'paymentMethod'   => $this->paymentMethod,
            'responsibleName' => $this->responsibleName,
            'notes'           => $this->notes,
            'status'          => $this->status,
            'cancelledAt'     => $this->cancelledAt,
            'cancelReason'    => $this->cancelReason,
            'cancelledBy'     => $this->cancelledBy,
            'createdAt'       => $this->createdAt,
            'items'           => array_map(function ($i) { return $i->toArray(); }, $this->items),
        ];
    }
}
