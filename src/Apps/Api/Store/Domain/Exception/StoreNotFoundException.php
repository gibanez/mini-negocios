<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Store\Domain\Exception;

use RuntimeException;

final class StoreNotFoundException extends RuntimeException
{
    public static function withId(string $id): self
    {
        return new self("Store not found: {$id}");
    }
}
