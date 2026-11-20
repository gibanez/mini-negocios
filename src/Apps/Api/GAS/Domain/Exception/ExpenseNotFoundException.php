<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\GAS\Domain\Exception;

use RuntimeException;

final class ExpenseNotFoundException extends RuntimeException
{
    public static function withId(string $id): self
    {
        return new self("Expense not found: {$id}");
    }
}
