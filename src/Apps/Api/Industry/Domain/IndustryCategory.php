<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Industry\Domain;

final class IndustryCategory
{
    /** @var string */ private $id;
    /** @var string */ private $industryId;
    /** @var string */ private $name;
    /** @var int */    private $position;

    private function __construct(string $id, string $industryId, string $name, int $position)
    {
        $this->id         = $id;
        $this->industryId = $industryId;
        $this->name       = $name;
        $this->position   = $position;
    }

    public static function fromPersistence(string $id, string $industryId, string $name, int $position): self
    {
        return new self($id, $industryId, $name, $position);
    }

    public function id(): string         { return $this->id; }
    public function industryId(): string { return $this->industryId; }
    public function name(): string       { return $this->name; }
    public function position(): int      { return $this->position; }
}
