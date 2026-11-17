<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Domain;

interface StoreCatalogRepositoryInterface
{
    /** @return StoreCategory[] */
    public function findCategoriesByStoreId(string $storeId): array;

    public function findCategoryById(string $id): ?StoreCategory;

    public function findCategoryByNameAndStoreId(string $name, string $storeId): ?StoreCategory;

    public function saveCategory(StoreCategory $category): void;

    public function countActiveProductsByCategoryId(string $categoryId): int;

    /** @return StoreProduct[] */
    public function findProductsByCategoryId(string $categoryId): array;

    /** @return StoreProduct[] */
    public function findProductsByStoreId(string $storeId, ?string $search = null, ?string $status = null): array;

    public function findProductById(string $id): ?StoreProduct;

    public function findProductByNameAndStoreId(string $name, string $storeId): ?StoreProduct;

    public function saveProduct(StoreProduct $product): void;

    public function saveStockAdjustment(StockAdjustment $adjustment): void;

    /** @return StockAdjustment[] */
    public function findStockHistoryByProductId(string $productId): array;
}
