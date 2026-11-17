<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\ListStores;

use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;

final class ListStoresHandler
{
    /** @var StoreRepositoryInterface */
    private $stores;

    public function __construct(StoreRepositoryInterface $stores)
    {
        $this->stores = $stores;
    }

    /** @return array<int,array<string,mixed>> */
    public function handle(ListStoresQuery $q): array
    {
        return array_map(function ($s) { return $s->toArray(); }, $this->stores->findAll());
    }
}
