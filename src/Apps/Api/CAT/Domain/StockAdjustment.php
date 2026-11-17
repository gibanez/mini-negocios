<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Domain;
use Ramsey\Uuid\Uuid;

final class StockAdjustment
{
    public const REASON_REPOSICION = 'reposicion';
    public const REASON_ROTURA     = 'rotura';
    public const REASON_RECUENTO   = 'recuento';
    public const REASON_ERROR      = 'error';

    /** @var string[] */
    public static $validReasons = ['reposicion', 'rotura', 'recuento', 'error'];

    /** @var string */      private $id;
    /** @var string */      private $storeProductId;
    /** @var string */      private $storeId;
    /** @var int */         private $previousStock;
    /** @var int */         private $newStock;
    /** @var int */         private $delta;
    /** @var string */      private $reason;
    /** @var string|null */ private $note;
    /** @var string */      private $responsibleName;
    /** @var string */      private $createdAt;

    private function __construct(string $id, string $storeProductId, string $storeId,
        int $previousStock, int $newStock, int $delta, string $reason,
        ?string $note, string $responsibleName, string $createdAt)
    {
        $this->id = $id; $this->storeProductId = $storeProductId; $this->storeId = $storeId;
        $this->previousStock = $previousStock; $this->newStock = $newStock; $this->delta = $delta;
        $this->reason = $reason; $this->note = $note;
        $this->responsibleName = $responsibleName; $this->createdAt = $createdAt;
    }

    public static function create(string $storeProductId, string $storeId,
        int $previousStock, int $newStock, string $reason, ?string $note, string $responsibleName): self
    {
        return new self(Uuid::uuid4()->toString(), $storeProductId, $storeId,
            $previousStock, $newStock, $newStock - $previousStock,
            $reason, $note, $responsibleName, date('Y-m-d H:i:s'));
    }

    public static function fromPersistence(string $id, string $storeProductId, string $storeId,
        int $previousStock, int $newStock, int $delta, string $reason,
        ?string $note, string $responsibleName, string $createdAt): self
    {
        return new self($id, $storeProductId, $storeId, $previousStock, $newStock,
            $delta, $reason, $note, $responsibleName, $createdAt);
    }

    public function id(): string              { return $this->id; }
    public function storeProductId(): string  { return $this->storeProductId; }
    public function storeId(): string         { return $this->storeId; }
    public function previousStock(): int      { return $this->previousStock; }
    public function newStock(): int           { return $this->newStock; }
    public function delta(): int              { return $this->delta; }
    public function reason(): string          { return $this->reason; }
    public function note(): ?string           { return $this->note; }
    public function responsibleName(): string { return $this->responsibleName; }
    public function createdAt(): string       { return $this->createdAt; }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return ['id'=>$this->id,'delta'=>$this->delta,'newStock'=>$this->newStock,
            'reason'=>$this->reason,'note'=>$this->note,
            'responsibleName'=>$this->responsibleName,'createdAt'=>$this->createdAt];
    }
}
