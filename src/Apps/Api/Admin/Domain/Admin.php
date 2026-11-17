<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Admin\Domain;

use Ramsey\Uuid\Uuid;

final class Admin
{
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const ROLE_OWNER    = 'owner';
    public const ROLE_VENDOR   = 'vendor';
    public const ROLE_PLATFORM = 'platform_admin';

    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $email;

    /** @var string */
    private $passwordHash;

    /** @var string */
    private $status;

    /** @var string */
    private $role;

    /** @var string|null */
    private $storeId;

    private function __construct(
        string $id,
        string $name,
        string $email,
        string $passwordHash,
        string $status,
        string $role,
        ?string $storeId
    ) {
        $this->id           = $id;
        $this->name         = $name;
        $this->email        = $email;
        $this->passwordHash = $passwordHash;
        $this->status       = $status;
        $this->role         = $role;
        $this->storeId      = $storeId;
    }

    public static function create(string $name, string $email, string $password): self
    {
        return new self(
            Uuid::uuid4()->toString(),
            $name,
            $email,
            (string) password_hash($password, PASSWORD_BCRYPT),
            self::STATUS_ACTIVE,
            self::ROLE_OWNER,
            null
        );
    }

    public static function createOwner(string $name, string $email, string $password, string $storeId): self
    {
        return new self(
            Uuid::uuid4()->toString(),
            $name,
            $email,
            (string) password_hash($password, PASSWORD_BCRYPT),
            self::STATUS_ACTIVE,
            self::ROLE_OWNER,
            $storeId
        );
    }

    public static function createVendor(string $name, string $email, string $password, string $storeId): self
    {
        return new self(
            Uuid::uuid4()->toString(),
            $name,
            $email,
            (string) password_hash($password, PASSWORD_BCRYPT),
            self::STATUS_ACTIVE,
            self::ROLE_VENDOR,
            $storeId
        );
    }

    public static function fromPersistence(
        string $id,
        string $name,
        string $email,
        string $passwordHash,
        string $status,
        string $role,
        ?string $storeId
    ): self {
        return new self($id, $name, $email, $passwordHash, $status, $role, $storeId);
    }

    public function id(): string           { return $this->id; }
    public function name(): string         { return $this->name; }
    public function email(): string        { return $this->email; }
    public function passwordHash(): string { return $this->passwordHash; }
    public function status(): string       { return $this->status; }
    public function role(): string         { return $this->role; }
    public function storeId(): ?string     { return $this->storeId; }

    public function isActive(): bool { return $this->status === self::STATUS_ACTIVE; }
    public function isOwner(): bool  { return $this->role === self::ROLE_OWNER; }
    public function isVendor(): bool { return $this->role === self::ROLE_VENDOR; }

    public function rename(string $name): void             { $this->name = $name; }
    public function changePassword(string $password): void { $this->passwordHash = (string) password_hash($password, PASSWORD_BCRYPT); }
    public function activate(): void                       { $this->status = self::STATUS_ACTIVE; }
    public function deactivate(): void                     { $this->status = self::STATUS_INACTIVE; }
    public function assignToStore(string $storeId): void   { $this->storeId = $storeId; }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'email'  => $this->email,
            'role'   => $this->role,
            'status' => $this->status,
        ];
    }
}
