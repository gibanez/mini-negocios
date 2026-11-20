<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Domain;

interface ExpenseRepositoryInterface
{
    public function findById(string $id): ?Expense;

    /** @return Expense[] */
    public function findByStoreId(string $storeId, ?string $dateFrom = null, ?string $dateTo = null, ?string $category = null): array;

    public function save(Expense $expense): void;

    public function delete(string $id): void;
}
