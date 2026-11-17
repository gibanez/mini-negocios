<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Domain;

use Ramsey\Uuid\Uuid;

final class SaleItem
{
    /** @var string */      private $id;
    /** @var string */      private $saleId;
    /** @var string|null */ private $storeProductId;
    /** @var string */      private $productName;
    /** @var int */         private $quantity;
    /** @var int */         private $unitPrice;
    /** @var int */         private $subtotal;

    private function __construct(string $id, string $saleId, ?string $storeProductId,
        string $productName, int $quantity, int $unitPrice, int $subtotal)
    {
        $this->id=$id; $this->saleId=$saleId; $this->storeProductId=$storeProductId;
        $this->productName=$productName; $this->quantity=$quantity;
        $this->unitPrice=$unitPrice; $this->subtotal=$subtotal;
    }

    public static function create(string $saleId, ?string $storeProductId,
        string $productName, int $quantity, int $unitPrice): self
    {
        $subtotal = (int) round($quantity * $unitPrice / 1000);
        return new self(Uuid::uuid4()->toString(), $saleId, $storeProductId,
            $productName, $quantity, $unitPrice, $subtotal);
    }

    public static function fromPersistence(string $id, string $saleId, ?string $storeProductId,
        string $productName, int $quantity, int $unitPrice, int $subtotal): self
    {
        return new self($id, $saleId, $storeProductId, $productName, $quantity, $unitPrice, $subtotal);
    }

    public function id(): string              { return $this->id; }
    public function saleId(): string          { return $this->saleId; }
    public function storeProductId(): ?string { return $this->storeProductId; }
    public function productName(): string     { return $this->productName; }
    public function quantity(): int           { return $this->quantity; }
    public function unitPrice(): int          { return $this->unitPrice; }
    public function subtotal(): int           { return $this->subtotal; }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return ['id'=>$this->id,'productName'=>$this->productName,'quantity'=>$this->quantity,
            'unitPrice'=>$this->unitPrice,'subtotal'=>$this->subtotal];
    }
}
