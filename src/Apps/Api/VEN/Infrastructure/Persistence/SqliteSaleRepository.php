<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Infrastructure\Persistence;

use Gaby\Apps\Api\VEN\Domain\Sale;
use Gaby\Apps\Api\VEN\Domain\SaleItem;
use Gaby\Apps\Api\VEN\Domain\SaleRepositoryInterface;
use PDO;

final class SqliteSaleRepository implements SaleRepositoryInterface
{
    /** @var PDO */ private $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function findById(string $id): ?Sale
    {
        $stmt = $this->pdo->prepare('SELECT * FROM sales WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $sale = $this->hydrate($row);
        $sale->setItems($this->findItemsBySaleId($id));
        return $sale;
    }

    public function findByStoreId(string $storeId, ?string $dateFrom = null, ?string $dateTo = null, ?string $clientId = null): array
    {
        $where  = ['store_id = :store_id'];
        $params = ['store_id' => $storeId];
        if ($dateFrom !== null) { $where[] = "date(created_at) >= :date_from"; $params['date_from'] = $dateFrom; }
        if ($dateTo   !== null) { $where[] = "date(created_at) <= :date_to";   $params['date_to']   = $dateTo; }
        if ($clientId !== null) { $where[] = 'client_id = :client_id';         $params['client_id'] = $clientId; }
        $sql  = 'SELECT * FROM sales WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function save(Sale $sale): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO sales (id, store_id, client_id, total, payment_method, responsible_id, responsible_name, notes, status, cancelled_at, cancel_reason, cancelled_by, created_at)
             VALUES (:id,:store_id,:client_id,:total,:pm,:resp_id,:resp_name,:notes,:status,:cancelled_at,:cancel_reason,:cancelled_by,:created_at)
             ON CONFLICT(id) DO UPDATE SET status=excluded.status, cancelled_at=excluded.cancelled_at,
             cancel_reason=excluded.cancel_reason, cancelled_by=excluded.cancelled_by'
        );
        $stmt->execute([
            'id'           => $sale->id(),
            'store_id'     => $sale->storeId(),
            'client_id'    => $sale->clientId(),
            'total'        => $sale->total(),
            'pm'           => $sale->paymentMethod(),
            'resp_id'      => $sale->responsibleId(),
            'resp_name'    => $sale->responsibleName(),
            'notes'        => $sale->notes(),
            'status'       => $sale->status(),
            'cancelled_at' => $sale->cancelledAt(),
            'cancel_reason'=> $sale->cancelReason(),
            'cancelled_by' => $sale->cancelledBy(),
            'created_at'   => $sale->createdAt(),
        ]);
    }

    public function saveItems(array $items): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO sale_items (id, sale_id, store_product_id, product_name, quantity, unit_price, subtotal)
             VALUES (:id,:sale_id,:prod_id,:name,:qty,:price,:subtotal)
             ON CONFLICT(id) DO NOTHING'
        );
        foreach ($items as $item) {
            $stmt->execute([
                'id'       => $item->id(),
                'sale_id'  => $item->saleId(),
                'prod_id'  => $item->storeProductId(),
                'name'     => $item->productName(),
                'qty'      => $item->quantity(),
                'price'    => $item->unitPrice(),
                'subtotal' => $item->subtotal(),
            ]);
        }
    }

    public function findItemsBySaleId(string $saleId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM sale_items WHERE sale_id = :id ORDER BY rowid ASC');
        $stmt->execute(['id' => $saleId]);
        return array_map([$this, 'hydrateItem'], $stmt->fetchAll());
    }

    /** @param array<string,mixed> $row */
    private function hydrate(array $row): Sale
    {
        return Sale::fromPersistence(
            (string)$row['id'],
            (string)$row['store_id'],
            isset($row['client_id'])    ? (string)$row['client_id']    : null,
            (int)$row['total'],
            (string)$row['payment_method'],
            (string)$row['responsible_id'],
            (string)$row['responsible_name'],
            isset($row['notes'])        ? (string)$row['notes']        : null,
            (string)$row['status'],
            isset($row['cancelled_at']) ? (string)$row['cancelled_at'] : null,
            isset($row['cancel_reason'])? (string)$row['cancel_reason']: null,
            isset($row['cancelled_by']) ? (string)$row['cancelled_by'] : null,
            (string)$row['created_at']
        );
    }

    /** @param array<string,mixed> $row */
    private function hydrateItem(array $row): SaleItem
    {
        return SaleItem::fromPersistence(
            (string)$row['id'],
            (string)$row['sale_id'],
            isset($row['store_product_id']) ? (string)$row['store_product_id'] : null,
            (string)$row['product_name'],
            (int)$row['quantity'],
            (int)$row['unit_price'],
            (int)$row['subtotal']
        );
    }
}
