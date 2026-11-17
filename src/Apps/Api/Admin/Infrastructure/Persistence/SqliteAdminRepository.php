<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Admin\Infrastructure\Persistence;

use Gaby\Apps\Api\Admin\Domain\Admin;
use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;
use PDO;

final class SqliteAdminRepository implements AdminRepositoryInterface
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(string $id): ?Admin
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admins WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $this->hydrate($row);
    }

    public function findByEmail(string $email): ?Admin
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admins WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row === false ? null : $this->hydrate($row);
    }

    public function findAllByStoreId(string $storeId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM admins WHERE store_id = :store_id ORDER BY rowid ASC'
        );
        $stmt->execute(['store_id' => $storeId]);
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function countActiveOwnersByStoreId(string $storeId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM admins WHERE store_id = :store_id AND role = 'owner' AND status = 'active'"
        );
        $stmt->execute(['store_id' => $storeId]);
        return (int) $stmt->fetchColumn();
    }

    public function save(Admin $admin): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO admins (id, name, email, password_hash, status, role, store_id)
             VALUES (:id, :name, :email, :password_hash, :status, :role, :store_id)
             ON CONFLICT(id) DO UPDATE SET
                name          = excluded.name,
                email         = excluded.email,
                password_hash = excluded.password_hash,
                status        = excluded.status,
                role          = excluded.role,
                store_id      = excluded.store_id'
        );

        $stmt->execute([
            'id'            => $admin->id(),
            'name'          => $admin->name(),
            'email'         => $admin->email(),
            'password_hash' => $admin->passwordHash(),
            'status'        => $admin->status(),
            'role'          => $admin->role(),
            'store_id'      => $admin->storeId(),
        ]);
    }

    public function deleteById(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM admins WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): Admin
    {
        return Admin::fromPersistence(
            (string) $row['id'],
            (string) $row['name'],
            (string) $row['email'],
            (string) $row['password_hash'],
            (string) $row['status'],
            (string) ($row['role'] ?? Admin::ROLE_OWNER),
            isset($row['store_id']) ? (string) $row['store_id'] : null
        );
    }
}
