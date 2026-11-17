<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\UpdateStore;

use Gaby\Apps\Api\Store\Domain\Exception\StoreNotFoundException;
use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;

final class UpdateStoreHandler
{
    /** @var StoreRepositoryInterface */
    private $stores;

    public function __construct(StoreRepositoryInterface $stores)
    {
        $this->stores = $stores;
    }

    /** @return array<string,mixed> */
    public function handle(UpdateStoreCommand $c): array
    {
        $store = $this->stores->findById($c->storeId);
        if ($store === null) {
            throw StoreNotFoundException::withId($c->storeId);
        }
        $store->rename($c->name);
        $this->stores->save($store);
        return $store->toArray();
    }
}
