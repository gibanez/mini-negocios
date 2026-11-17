<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Store\Domain;

interface StoreRepositoryInterface
{
    public function findById(string $id): ?Store;

    /** @return Store[] */
    public function findAll(): array;

    public function save(Store $store): void;
}
