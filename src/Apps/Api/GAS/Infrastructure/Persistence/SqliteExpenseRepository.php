<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Infrastructure\Persistence;

use Gaby\Apps\Api\GAS\Domain\Expense;
use Gaby\Apps\Api\GAS\Domain\ExpenseRepositoryInterface;
use PDO;

final class SqliteExpenseRepository implements ExpenseRepositoryInterface
{
    /** @var PDO */ private $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function findById(string $id): ?Expense
    {
        $stmt = $this->pdo->prepare('SELECT * FROM expenses WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    /** @return Expense[] */
    public function findByStoreId(string $storeId, ?string $dateFrom = null, ?string $dateTo = null, ?string $category = null): array
    {
        $where  = ['store_id = :store_id'];
        $params = ['store_id' => $storeId];
        if ($dateFrom !== null) { $where[] = 'date >= :date_from'; $params['date_from'] = $dateFrom; }
        if ($dateTo   !== null) { $where[] = 'date <= :date_to';   $params['date_to']   = $dateTo; }
        if ($category !== null) { $where[] = 'category = :cat';    $params['cat']       = $category; }
        $sql  = 'SELECT * FROM expenses WHERE ' . implode(' AND ', $where) . ' ORDER BY date DESC, created_at DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function save(Expense $expense): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO expenses
                (id, store_id, amount, category, recipient, description, payment_method,
                 responsible_id, responsible_name, date, created_at)
             VALUES
                (:id,:store_id,:amount,:category,:recipient,:description,:pm,
                 :resp_id,:resp_name,:date,:created_at)
             ON CONFLICT(id) DO UPDATE SET
                amount=excluded.amount, category=excluded.category,
                recipient=excluded.recipient, description=excluded.description,
                payment_method=excluded.payment_method, date=excluded.date'
        );
        $stmt->execute([
            'id'          => $expense->id(),
            'store_id'    => $expense->storeId(),
            'amount'      => $expense->amount(),
            'category'    => $expense->category(),
            'recipient'   => $expense->recipient(),
            'description' => $expense->description(),
            'pm'          => $expense->paymentMethod(),
            'resp_id'     => $expense->responsibleId(),
            'resp_name'   => $expense->responsibleName(),
            'date'        => $expense->date(),
            'created_at'  => $expense->createdAt(),
        ]);
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM expenses WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /** @param array<string,mixed> $row */
    private function hydrate(array $row): Expense
    {
        return Expense::fromPersistence(
            (string)$row['id'],
            (string)$row['store_id'],
            (int)$row['amount'],
            (string)$row['category'],
            (string)$row['recipient'],
            isset($row['description']) ? (string)$row['description'] : null,
            (string)$row['payment_method'],
            (string)$row['responsible_id'],
            (string)$row['responsible_name'],
            (string)$row['date'],
            (string)$row['created_at']
        );
    }
}
