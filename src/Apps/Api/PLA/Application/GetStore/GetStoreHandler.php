<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\GetStore;

use Gaby\Apps\Api\Store\Domain\Exception\StoreNotFoundException;
use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;

final class GetStoreHandler
{
    /** @var StoreRepositoryInterface */
    private $stores;

    public function __construct(StoreRepositoryInterface $stores)
    {
        $this->stores = $stores;
    }

    /** @return array<string,mixed> */
    public function handle(GetStoreQuery $q): array
    {
        $store = $this->stores->findById($q->storeId);
        if ($store === null) {
            throw StoreNotFoundException::withId($q->storeId);
        }
        return $store->toArray();
    }
}
