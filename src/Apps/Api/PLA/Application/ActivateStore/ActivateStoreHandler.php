<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\ActivateStore;

use Gaby\Apps\Api\Store\Domain\Exception\StoreNotFoundException;
use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;

final class ActivateStoreHandler
{
    /** @var StoreRepositoryInterface */
    private $stores;

    public function __construct(StoreRepositoryInterface $stores)
    {
        $this->stores = $stores;
    }

    public function handle(ActivateStoreCommand $cmd): void
    {
        $store = $this->stores->findById($cmd->storeId);
        if ($store === null) {
            throw StoreNotFoundException::withId($cmd->storeId);
        }
        $store->activate();
        $this->stores->save($store);
    }
}
