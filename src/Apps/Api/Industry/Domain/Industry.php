<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Industry\Domain;

final class Industry
{
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /** @var string */      private $id;
    /** @var string */      private $name;
    /** @var string|null */ private $description;
    /** @var string */      private $status;

    private function __construct(string $id, string $name, ?string $description, string $status)
    {
        $this->id          = $id;
        $this->name        = $name;
        $this->description = $description;
        $this->status      = $status;
    }

    public static function fromPersistence(string $id, string $name, ?string $description, string $status): self
    {
        return new self($id, $name, $description, $status);
    }

    public function id(): string           { return $this->id; }
    public function name(): string         { return $this->name; }
    public function description(): ?string { return $this->description; }
    public function status(): string       { return $this->status; }
    public function isActive(): bool       { return $this->status === self::STATUS_ACTIVE; }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'description' => $this->description];
    }
}
