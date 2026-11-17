<?php

declare(strict_types=1);

namespace Gaby\Tests\Apps\Api\Auth\Doubles;

use Gaby\Apps\Api\Store\Domain\Store;
use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;

final class InMemoryStoreRepository implements StoreRepositoryInterface
{
    /** @var array<string, Store> */
    private $stores = [];

    public function findById(string $id): ?Store
    {
        return $this->stores[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->stores);
    }

    public function save(Store $store): void
    {
        $this->stores[$store->id()] = $store;
    }
}
