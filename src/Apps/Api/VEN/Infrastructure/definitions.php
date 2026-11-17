<?php

declare(strict_types=1);

use function DI\autowire;
use Gaby\Apps\Api\VEN\Domain\SaleRepositoryInterface;
use Gaby\Apps\Api\VEN\Infrastructure\Persistence\SqliteSaleRepository;

return [
    SaleRepositoryInterface::class => autowire(SqliteSaleRepository::class),
];
