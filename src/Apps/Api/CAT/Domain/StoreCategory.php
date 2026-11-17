<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CAT\Domain;

use Ramsey\Uuid\Uuid;

final class StoreCategory
{
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /** @var string */ private $id;
    /** @var string */ private $storeId;
    /** @var string */ private $name;
    /** @var string */ private $status;
    /** @var int */    private $position;

    private function __construct(string $id, string $storeId, string $name, string $status, int $position)
    {
        $this->id       = $id;
        $this->storeId  = $storeId;
        $this->name     = $name;
        $this->status   = $status;
        $this->position = $position;
    }

    public static function create(string $storeId, string $name, int $position = 0): self
    {
        return new self(Uuid::uuid4()->toString(), $storeId, $name, self::STATUS_ACTIVE, $position);
    }

    public static function fromPersistence(string $id, string $storeId, string $name, string $status, int $position): self
    {
        return new self($id, $storeId, $name, $status, $position);
    }

    public function id(): string      { return $this->id; }
    public function storeId(): string { return $this->storeId; }
    public function name(): string    { return $this->name; }
    public function status(): string  { return $this->status; }
    public function position(): int   { return $this->position; }
    public function isActive(): bool  { return $this->status === self::STATUS_ACTIVE; }

    public function rename(string $name): void  { $this->name = $name; }
    public function deactivate(): void          { $this->status = self::STATUS_INACTIVE; }
    public function activate(): void            { $this->status = self::STATUS_ACTIVE; }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'status'   => $this->status,
            'position' => $this->position,
        ];
    }
}
