<?php
declare(strict_types=1);
use function DI\autowire;
use Gaby\Apps\Api\FIA\Domain\CreditAccountRepositoryInterface;
use Gaby\Apps\Api\FIA\Infrastructure\Persistence\SqliteCreditAccountRepository;

return [
    CreditAccountRepositoryInterface::class => autowire(SqliteCreditAccountRepository::class),
];
