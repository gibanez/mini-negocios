<?php

declare(strict_types=1);

use function DI\autowire;
use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;
use Gaby\Apps\Api\CAT\Infrastructure\Persistence\SqliteStoreCatalogRepository;

return [
    StoreCatalogRepositoryInterface::class => autowire(SqliteStoreCatalogRepository::class),
];
