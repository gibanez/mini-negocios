<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Domain;
use Ramsey\Uuid\Uuid;

final class CreditMovement
{
    public const TYPE_PURCHASE = 'purchase';
    public const TYPE_PAYMENT  = 'payment';
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_CANCELLED = 'cancelled';

    /** @var string */      private $id;
    /** @var string */      private $creditAccountId;
    /** @var string */      private $storeId;
    /** @var string */      private $type;
    /** @var int */         private $amount;
    /** @var string|null */ private $saleId;
    /** @var string|null */ private $paymentMethod;
    /** @var string|null */ private $notes;
    /** @var string */      private $status;
    /** @var string|null */ private $cancelledAt;
    /** @var string|null */ private $cancelReason;
    /** @var string|null */ private $cancelledBy;
    /** @var string */      private $createdAt;

    private function __construct(string $id, string $creditAccountId, string $storeId,
        string $type, int $amount, ?string $saleId, ?string $paymentMethod, ?string $notes,
        string $status, ?string $cancelledAt, ?string $cancelReason, ?string $cancelledBy, string $createdAt)
    {
        $this->id=$id; $this->creditAccountId=$creditAccountId; $this->storeId=$storeId;
        $this->type=$type; $this->amount=$amount; $this->saleId=$saleId;
        $this->paymentMethod=$paymentMethod; $this->notes=$notes; $this->status=$status;
        $this->cancelledAt=$cancelledAt; $this->cancelReason=$cancelReason;
        $this->cancelledBy=$cancelledBy; $this->createdAt=$createdAt;
    }

    public static function createDebt(string $creditAccountId, string $storeId,
        int $amount, ?string $saleId, ?string $notes): self
    {
        return new self(Uuid::uuid4()->toString(), $creditAccountId, $storeId,
            self::TYPE_PURCHASE, $amount, $saleId, null, $notes,
            self::STATUS_ACTIVE, null, null, null, date('Y-m-d H:i:s'));
    }

    public static function createPayment(string $creditAccountId, string $storeId,
        int $amount, string $paymentMethod, ?string $notes): self
    {
        return new self(Uuid::uuid4()->toString(), $creditAccountId, $storeId,
            self::TYPE_PAYMENT, $amount, null, $paymentMethod, $notes,
            self::STATUS_ACTIVE, null, null, null, date('Y-m-d H:i:s'));
    }

    public static function fromPersistence(string $id, string $creditAccountId, string $storeId,
        string $type, int $amount, ?string $saleId, ?string $paymentMethod, ?string $notes,
        string $status, ?string $cancelledAt, ?string $cancelReason, ?string $cancelledBy, string $createdAt): self
    {
        return new self($id, $creditAccountId, $storeId, $type, $amount, $saleId,
            $paymentMethod, $notes, $status, $cancelledAt, $cancelReason, $cancelledBy, $createdAt);
    }

    public function id(): string              { return $this->id; }
    public function creditAccountId(): string { return $this->creditAccountId; }
    public function storeId(): string         { return $this->storeId; }
    public function type(): string            { return $this->type; }
    public function amount(): int             { return $this->amount; }
    public function saleId(): ?string         { return $this->saleId; }
    public function paymentMethod(): ?string  { return $this->paymentMethod; }
    public function notes(): ?string          { return $this->notes; }
    public function status(): string          { return $this->status; }
    public function isActive(): bool          { return $this->status === self::STATUS_ACTIVE; }
    public function cancelledAt(): ?string    { return $this->cancelledAt; }
    public function cancelReason(): ?string   { return $this->cancelReason; }
    public function cancelledBy(): ?string    { return $this->cancelledBy; }
    public function createdAt(): string       { return $this->createdAt; }

    public function cancel(string $reason, string $cancelledBy): void
    {
        $this->status      = self::STATUS_CANCELLED;
        $this->cancelReason  = $reason;
        $this->cancelledBy = $cancelledBy;
        $this->cancelledAt = date('Y-m-d H:i:s');
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return ['id'=>$this->id,'type'=>$this->type,'amount'=>$this->amount,
            'paymentMethod'=>$this->paymentMethod,'notes'=>$this->notes,
            'status'=>$this->status,'cancelledAt'=>$this->cancelledAt,
            'cancelReason'=>$this->cancelReason,'createdAt'=>$this->createdAt];
    }
}
