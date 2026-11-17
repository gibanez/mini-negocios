<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Domain;
use Ramsey\Uuid\Uuid;

final class CreditAccount
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';

    /** @var string */ private $id;
    /** @var string */ private $storeId;
    /** @var string */ private $clientId;
    /** @var string */ private $publicToken;
    /** @var string */ private $status;
    /** @var string */ private $createdAt;

    private function __construct(string $id, string $storeId, string $clientId,
        string $publicToken, string $status, string $createdAt)
    {
        $this->id=$id; $this->storeId=$storeId; $this->clientId=$clientId;
        $this->publicToken=$publicToken; $this->status=$status; $this->createdAt=$createdAt;
    }

    public static function create(string $storeId, string $clientId): self
    {
        return new self(Uuid::uuid4()->toString(), $storeId, $clientId,
            Uuid::uuid4()->toString(), self::STATUS_ACTIVE, date('Y-m-d H:i:s'));
    }

    public static function fromPersistence(string $id, string $storeId, string $clientId,
        string $publicToken, string $status, string $createdAt): self
    {
        return new self($id, $storeId, $clientId, $publicToken, $status, $createdAt);
    }

    public function id(): string          { return $this->id; }
    public function storeId(): string     { return $this->storeId; }
    public function clientId(): string    { return $this->clientId; }
    public function publicToken(): string { return $this->publicToken; }
    public function status(): string      { return $this->status; }
    public function createdAt(): string   { return $this->createdAt; }

    public function renewToken(): void { $this->publicToken = Uuid::uuid4()->toString(); }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return ['id'=>$this->id,'clientId'=>$this->clientId,'publicToken'=>$this->publicToken,
            'status'=>$this->status,'createdAt'=>$this->createdAt];
    }
}
