<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ONB\Application\SetStoreName;

use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;
use Gaby\Apps\Api\Store\Domain\Exception\StoreNotFoundException;

final class SetStoreNameHandler
{
    /** @var StoreRepositoryInterface */
    private $stores;

    public function __construct(StoreRepositoryInterface $stores)
    {
        $this->stores = $stores;
    }

    public function handle(SetStoreNameCommand $cmd): void
    {
        $store = $this->stores->findById($cmd->storeId);
        if ($store === null) {
            throw StoreNotFoundException::withId($cmd->storeId);
        }

        $store->rename($cmd->name);
        $this->stores->save($store);
    }
}
