<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CAT\Domain;

use Ramsey\Uuid\Uuid;

final class StoreProduct
{
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /** @var string */      private $id;
    /** @var string */      private $storeId;
    /** @var string */      private $categoryId;
    /** @var string */      private $name;
    /** @var string|null */ private $brand;
    /** @var string */      private $unit;
    /** @var int|null */    private $price;
    /** @var int */         private $stock;
    /** @var string */      private $status;

    private function __construct(
        string $id,
        string $storeId,
        string $categoryId,
        string $name,
        ?string $brand,
        string $unit,
        ?int $price,
        int $stock,
        string $status
    ) {
        $this->id         = $id;
        $this->storeId    = $storeId;
        $this->categoryId = $categoryId;
        $this->name       = $name;
        $this->brand      = $brand;
        $this->unit       = $unit;
        $this->price      = $price;
        $this->stock      = $stock;
        $this->status     = $status;
    }

    public static function createFromTemplate(string $storeId, string $categoryId, string $name, string $unit): self
    {
        return new self(Uuid::uuid4()->toString(), $storeId, $categoryId, $name, null, $unit, null, 0, self::STATUS_ACTIVE);
    }

    public static function fromPersistence(
        string $id,
        string $storeId,
        string $categoryId,
        string $name,
        ?string $brand,
        string $unit,
        ?int $price,
        int $stock,
        string $status
    ): self {
        return new self($id, $storeId, $categoryId, $name, $brand, $unit, $price, $stock, $status);
    }

    public function id(): string         { return $this->id; }
    public function storeId(): string    { return $this->storeId; }
    public function categoryId(): string { return $this->categoryId; }
    public function name(): string       { return $this->name; }
    public function brand(): ?string     { return $this->brand; }
    public function unit(): string       { return $this->unit; }
    public function price(): ?int        { return $this->price; }
    public function stock(): int         { return $this->stock; }
    public function status(): string     { return $this->status; }
    public function isActive(): bool     { return $this->status === self::STATUS_ACTIVE; }
    public function hasPrice(): bool     { return $this->price !== null; }

    public function setPrice(?int $price): void    { $this->price = $price; }
    public function setStock(int $stock): void      { $this->stock = $stock; }
    public function rename(string $name): void      { $this->name = $name; }
    public function setBrand(?string $brand): void  { $this->brand = $brand; }
    public function deactivate(): void              { $this->status = self::STATUS_INACTIVE; }
    public function activate(): void                { $this->status = self::STATUS_ACTIVE; }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'categoryId' => $this->categoryId,
            'name'       => $this->name,
            'brand'      => $this->brand,
            'unit'       => $this->unit,
            'price'      => $this->price,
            'stock'      => $this->stock,
            'status'     => $this->status,
        ];
    }
}
