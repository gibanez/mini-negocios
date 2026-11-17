<?php

declare(strict_types=1);

use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;
use Gaby\Apps\Api\Store\Infrastructure\Persistence\SqliteStoreRepository;
use function DI\autowire;

return [
    StoreRepositoryInterface::class => autowire(SqliteStoreRepository::class),
];
