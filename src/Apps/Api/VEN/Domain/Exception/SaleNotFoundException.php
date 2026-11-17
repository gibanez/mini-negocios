<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Domain\Exception;

use RuntimeException;

final class SaleNotFoundException extends RuntimeException
{
    public static function withId(string $id): self { return new self("Sale not found: {$id}"); }
}
