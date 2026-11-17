<?php

declare(strict_types=1);

use Gaby\Apps\Api\Industry\Domain\IndustryRepositoryInterface;
use Gaby\Apps\Api\Industry\Infrastructure\Persistence\SqliteIndustryRepository;
use function DI\autowire;

return [
    IndustryRepositoryInterface::class => autowire(SqliteIndustryRepository::class),
];
