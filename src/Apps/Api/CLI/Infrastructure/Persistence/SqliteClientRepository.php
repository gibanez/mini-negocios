<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CLI\Infrastructure\Persistence;

use Gaby\Apps\Api\CLI\Domain\Client;
use Gaby\Apps\Api\CLI\Domain\ClientRepositoryInterface;
use PDO;

final class SqliteClientRepository implements ClientRepositoryInterface
{
    /** @var PDO */ private $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function findById(string $id): ?Client
    {
        $stmt = $this->pdo->prepare('SELECT * FROM clients WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function findByStoreId(string $storeId, ?string $search = null): array
    {
        $sql = 'SELECT * FROM clients WHERE store_id = :store_id';
        $params = ['store_id' => $storeId];
        if ($search !== null && $search !== '') {
            $sql .= ' AND (LOWER(name) LIKE :s OR LOWER(nickname) LIKE :s2)';
            $params['s']  = '%' . strtolower($search) . '%';
            $params['s2'] = '%' . strtolower($search) . '%';
        }
        $sql .= ' ORDER BY rowid ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function save(Client $client): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO clients (id, store_id, name, nickname, phone, address, notes, status, created_at)
             VALUES (:id,:store_id,:name,:nickname,:phone,:address,:notes,:status,:created_at)
             ON CONFLICT(id) DO UPDATE SET name=excluded.name, nickname=excluded.nickname,
             phone=excluded.phone, address=excluded.address, notes=excluded.notes, status=excluded.status'
        );
        $stmt->execute([
            'id'         => $client->id(),
            'store_id'   => $client->storeId(),
            'name'       => $client->name(),
            'nickname'   => $client->nickname(),
            'phone'      => $client->phone(),
            'address'    => $client->address(),
            'notes'      => $client->notes(),
            'status'     => $client->status(),
            'created_at' => $client->createdAt(),
        ]);
    }

    /** @param array<string,mixed> $row */
    private function hydrate(array $row): Client
    {
        return Client::fromPersistence(
            (string)$row['id'],
            (string)$row['store_id'],
            (string)$row['name'],
            isset($row['nickname']) ? (string)$row['nickname'] : null,
            isset($row['phone'])    ? (string)$row['phone']    : null,
            isset($row['address'])  ? (string)$row['address']  : null,
            isset($row['notes'])    ? (string)$row['notes']    : null,
            (string)$row['status'],
            (string)$row['created_at']
        );
    }
}
