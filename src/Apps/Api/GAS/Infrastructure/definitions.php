<?php

declare(strict_types=1);

use function DI\autowire;
use Gaby\Apps\Api\GAS\Domain\ExpenseRepositoryInterface;
use Gaby\Apps\Api\GAS\Infrastructure\Persistence\SqliteExpenseRepository;

return [
    ExpenseRepositoryInterface::class => autowire(SqliteExpenseRepository::class),
];
