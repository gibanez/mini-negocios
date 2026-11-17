<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Store\Infrastructure\Persistence;

use Gaby\Apps\Api\Store\Domain\Store;
use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;
use PDO;

final class SqliteStoreRepository implements StoreRepositoryInterface
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM stores ORDER BY created_at DESC');
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function findById(string $id): ?Store
    {
        $stmt = $this->pdo->prepare('SELECT * FROM stores WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $this->hydrate($row);
    }

    public function save(Store $store): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO stores (id, name, industry, status, created_at, onboarding_completed, theme, logo_path)
             VALUES (:id, :name, :industry, :status, :created_at, :onboarding, :theme, :logo)
             ON CONFLICT(id) DO UPDATE SET
                name                 = excluded.name,
                industry             = excluded.industry,
                status               = excluded.status,
                onboarding_completed = excluded.onboarding_completed,
                theme                = excluded.theme,
                logo_path            = excluded.logo_path'
        );

        $stmt->execute([
            'id'         => $store->id(),
            'name'       => $store->name(),
            'industry'   => $store->industry(),
            'status'     => $store->status(),
            'created_at' => $store->createdAt(),
            'onboarding' => $store->onboardingCompleted() ? 1 : 0,
            'theme'      => $store->theme(),
            'logo'       => $store->logoPath(),
        ]);
    }

    /**
     * @param array<string,mixed> $row
     */
    private function hydrate(array $row): Store
    {
        return Store::fromPersistence(
            (string) $row['id'],
            (string) $row['name'],
            isset($row['industry']) ? (string) $row['industry'] : null,
            (string) $row['status'],
            (string) $row['created_at'],
            (bool) ($row['onboarding_completed'] ?? false),
            (string) ($row['theme'] ?? 'verde'),
            isset($row['logo_path']) ? (string) $row['logo_path'] : null
        );
    }
}
