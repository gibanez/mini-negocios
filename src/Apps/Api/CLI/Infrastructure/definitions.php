<?php

declare(strict_types=1);

use function DI\autowire;
use Gaby\Apps\Api\CLI\Domain\ClientRepositoryInterface;
use Gaby\Apps\Api\CLI\Infrastructure\Persistence\SqliteClientRepository;

return [
    ClientRepositoryInterface::class => autowire(SqliteClientRepository::class),
];
