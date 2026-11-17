<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CLI\Domain;

use Ramsey\Uuid\Uuid;

final class Client
{
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /** @var string */      private $id;
    /** @var string */      private $storeId;
    /** @var string */      private $name;
    /** @var string|null */ private $nickname;
    /** @var string|null */ private $phone;
    /** @var string|null */ private $address;
    /** @var string|null */ private $notes;
    /** @var string */      private $status;
    /** @var string */      private $createdAt;

    private function __construct(string $id, string $storeId, string $name,
        ?string $nickname, ?string $phone, ?string $address, ?string $notes,
        string $status, string $createdAt)
    {
        $this->id=$id; $this->storeId=$storeId; $this->name=$name;
        $this->nickname=$nickname; $this->phone=$phone; $this->address=$address;
        $this->notes=$notes; $this->status=$status; $this->createdAt=$createdAt;
    }

    public static function create(string $storeId, string $name, ?string $nickname,
        ?string $phone, ?string $address, ?string $notes): self
    {
        return new self(Uuid::uuid4()->toString(), $storeId, $name,
            $nickname, $phone, $address, $notes, self::STATUS_ACTIVE, date('Y-m-d H:i:s'));
    }

    public static function fromPersistence(string $id, string $storeId, string $name,
        ?string $nickname, ?string $phone, ?string $address, ?string $notes,
        string $status, string $createdAt): self
    {
        return new self($id, $storeId, $name, $nickname, $phone, $address, $notes, $status, $createdAt);
    }

    public function id(): string        { return $this->id; }
    public function storeId(): string   { return $this->storeId; }
    public function name(): string      { return $this->name; }
    public function nickname(): ?string { return $this->nickname; }
    public function phone(): ?string    { return $this->phone; }
    public function address(): ?string  { return $this->address; }
    public function notes(): ?string    { return $this->notes; }
    public function status(): string    { return $this->status; }
    public function isActive(): bool    { return $this->status === self::STATUS_ACTIVE; }
    public function createdAt(): string { return $this->createdAt; }

    public function update(string $name, ?string $nickname, ?string $phone,
        ?string $address, ?string $notes): void
    {
        $this->name=$name; $this->nickname=$nickname; $this->phone=$phone;
        $this->address=$address; $this->notes=$notes;
    }

    public function deactivate(): void { $this->status = self::STATUS_INACTIVE; }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return ['id'=>$this->id,'name'=>$this->name,'nickname'=>$this->nickname,
            'phone'=>$this->phone,'address'=>$this->address,'notes'=>$this->notes,
            'status'=>$this->status,'createdAt'=>$this->createdAt];
    }
}
