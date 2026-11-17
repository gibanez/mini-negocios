<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Industry\Domain;

final class IndustryProduct
{
    /** @var string */ private $id;
    /** @var string */ private $industryCategoryId;
    /** @var string */ private $name;
    /** @var string */ private $unit;
    /** @var int */    private $position;

    private function __construct(string $id, string $industryCategoryId, string $name, string $unit, int $position)
    {
        $this->id                 = $id;
        $this->industryCategoryId = $industryCategoryId;
        $this->name               = $name;
        $this->unit               = $unit;
        $this->position           = $position;
    }

    public static function fromPersistence(string $id, string $industryCategoryId, string $name, string $unit, int $position): self
    {
        return new self($id, $industryCategoryId, $name, $unit, $position);
    }

    public function id(): string                 { return $this->id; }
    public function industryCategoryId(): string { return $this->industryCategoryId; }
    public function name(): string               { return $this->name; }
    public function unit(): string               { return $this->unit; }
    public function position(): int              { return $this->position; }
}
