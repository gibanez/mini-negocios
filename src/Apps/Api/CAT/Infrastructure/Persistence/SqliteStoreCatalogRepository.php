<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Infrastructure\Persistence;

use Gaby\Apps\Api\CAT\Domain\StockAdjustment;
use Gaby\Apps\Api\CAT\Domain\StoreCategory;
use Gaby\Apps\Api\CAT\Domain\StoreProduct;
use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;
use PDO;

final class SqliteStoreCatalogRepository implements StoreCatalogRepositoryInterface
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findCategoriesByStoreId(string $storeId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM store_categories WHERE store_id = :s AND status = 'active' ORDER BY position ASC, rowid ASC"
        );
        $stmt->execute(['s' => $storeId]);
        return array_map([$this, 'hydrateCategory'], $stmt->fetchAll());
    }

    public function findCategoryById(string $id): ?StoreCategory
    {
        $stmt = $this->pdo->prepare('SELECT * FROM store_categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $this->hydrateCategory($row);
    }

    public function findCategoryByNameAndStoreId(string $name, string $storeId): ?StoreCategory
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM store_categories WHERE LOWER(name) = LOWER(:name) AND store_id = :s'
        );
        $stmt->execute(['name' => $name, 's' => $storeId]);
        $row = $stmt->fetch();
        return $row === false ? null : $this->hydrateCategory($row);
    }

    public function saveCategory(StoreCategory $cat): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO store_categories (id, store_id, name, status, position)
             VALUES (:id, :store_id, :name, :status, :position)
             ON CONFLICT(id) DO UPDATE SET
                name     = excluded.name,
                status   = excluded.status,
                position = excluded.position'
        );
        $stmt->execute([
            'id'       => $cat->id(),
            'store_id' => $cat->storeId(),
            'name'     => $cat->name(),
            'status'   => $cat->status(),
            'position' => $cat->position(),
        ]);
    }

    public function countActiveProductsByCategoryId(string $categoryId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM store_products WHERE store_category_id = :id AND status = 'active'"
        );
        $stmt->execute(['id' => $categoryId]);
        return (int) $stmt->fetchColumn();
    }

    public function findProductsByCategoryId(string $categoryId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM store_products WHERE store_category_id = :id AND status = 'active' ORDER BY rowid ASC"
        );
        $stmt->execute(['id' => $categoryId]);
        return array_map([$this, 'hydrateProduct'], $stmt->fetchAll());
    }

    public function findProductsByStoreId(string $storeId, ?string $search = null, ?string $status = null): array
    {
        $sql    = 'SELECT * FROM store_products WHERE store_id = :store_id';
        $params = ['store_id' => $storeId];
        if ($search !== null && $search !== '') {
            $sql .= ' AND LOWER(name) LIKE :search';
            $params['search'] = '%' . strtolower($search) . '%';
        }
        if ($status !== null) {
            $sql .= ' AND status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY rowid ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return array_map([$this, 'hydrateProduct'], $stmt->fetchAll());
    }

    public function findProductById(string $id): ?StoreProduct
    {
        $stmt = $this->pdo->prepare('SELECT * FROM store_products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $this->hydrateProduct($row);
    }

    public function findProductByNameAndStoreId(string $name, string $storeId): ?StoreProduct
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM store_products WHERE LOWER(name) = LOWER(:name) AND store_id = :s'
        );
        $stmt->execute(['name' => $name, 's' => $storeId]);
        $row = $stmt->fetch();
        return $row === false ? null : $this->hydrateProduct($row);
    }

    public function saveProduct(StoreProduct $p): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO store_products (id, store_id, store_category_id, name, brand, unit, price, stock, status)
             VALUES (:id, :store_id, :cat_id, :name, :brand, :unit, :price, :stock, :status)
             ON CONFLICT(id) DO UPDATE SET
                name              = excluded.name,
                brand             = excluded.brand,
                unit              = excluded.unit,
                price             = excluded.price,
                stock             = excluded.stock,
                status            = excluded.status,
                store_category_id = excluded.store_category_id'
        );
        $stmt->execute([
            'id'       => $p->id(),
            'store_id' => $p->storeId(),
            'cat_id'   => $p->categoryId(),
            'name'     => $p->name(),
            'brand'    => $p->brand(),
            'unit'     => $p->unit(),
            'price'    => $p->price(),
            'stock'    => $p->stock(),
            'status'   => $p->status(),
        ]);
    }

    public function saveStockAdjustment(StockAdjustment $adj): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO stock_history (id, store_product_id, store_id, previous_stock, new_stock, delta, reason, note, responsible_name, created_at)
             VALUES (:id, :prod_id, :store_id, :prev, :new, :delta, :reason, :note, :name, :created_at)'
        );
        $stmt->execute([
            'id'         => $adj->id(),
            'prod_id'    => $adj->storeProductId(),
            'store_id'   => $adj->storeId(),
            'prev'       => $adj->previousStock(),
            'new'        => $adj->newStock(),
            'delta'      => $adj->delta(),
            'reason'     => $adj->reason(),
            'note'       => $adj->note(),
            'name'       => $adj->responsibleName(),
            'created_at' => $adj->createdAt(),
        ]);
    }

    public function findStockHistoryByProductId(string $productId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM stock_history WHERE store_product_id = :id ORDER BY created_at DESC'
        );
        $stmt->execute(['id' => $productId]);
        return array_map([$this, 'hydrateAdjustment'], $stmt->fetchAll());
    }

    /** @param array<string,mixed> $row */
    private function hydrateCategory(array $row): StoreCategory
    {
        return StoreCategory::fromPersistence(
            (string) $row['id'],
            (string) $row['store_id'],
            (string) $row['name'],
            (string) $row['status'],
            (int) $row['position']
        );
    }

    /** @param array<string,mixed> $row */
    private function hydrateProduct(array $row): StoreProduct
    {
        return StoreProduct::fromPersistence(
            (string) $row['id'],
            (string) $row['store_id'],
            (string) $row['store_category_id'],
            (string) $row['name'],
            isset($row['brand']) ? (string) $row['brand'] : null,
            (string) $row['unit'],
            $row['price'] !== null ? (int) $row['price'] : null,
            (int) $row['stock'],
            (string) $row['status']
        );
    }

    /** @param array<string,mixed> $row */
    private function hydrateAdjustment(array $row): StockAdjustment
    {
        return StockAdjustment::fromPersistence(
            (string) $row['id'],
            (string) $row['store_product_id'],
            (string) $row['store_id'],
            (int) $row['previous_stock'],
            (int) $row['new_stock'],
            (int) $row['delta'],
            (string) $row['reason'],
            isset($row['note']) ? (string) $row['note'] : null,
            (string) $row['responsible_name'],
            (string) $row['created_at']
        );
    }
}
