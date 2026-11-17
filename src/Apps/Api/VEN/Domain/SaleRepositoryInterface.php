<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Domain;

interface SaleRepositoryInterface
{
    public function findById(string $id): ?Sale;
    /** @return Sale[] */
    public function findByStoreId(string $storeId, ?string $dateFrom = null, ?string $dateTo = null, ?string $clientId = null): array;
    public function save(Sale $sale): void;
    /** @param SaleItem[] $items */
    public function saveItems(array $items): void;
    /** @return SaleItem[] */
    public function findItemsBySaleId(string $saleId): array;
}
