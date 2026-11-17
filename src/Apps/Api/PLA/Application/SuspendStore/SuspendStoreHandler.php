<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\SuspendStore;

use Gaby\Apps\Api\Store\Domain\Exception\StoreNotFoundException;
use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;

final class SuspendStoreHandler
{
    /** @var StoreRepositoryInterface */
    private $stores;

    public function __construct(StoreRepositoryInterface $stores)
    {
        $this->stores = $stores;
    }

    public function handle(SuspendStoreCommand $cmd): void
    {
        $store = $this->stores->findById($cmd->storeId);
        if ($store === null) {
            throw StoreNotFoundException::withId($cmd->storeId);
        }
        $store->suspend();
        $this->stores->save($store);
    }
}
