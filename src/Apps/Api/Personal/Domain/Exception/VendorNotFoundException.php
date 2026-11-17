<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Personal\Domain\Exception;

use RuntimeException;

final class VendorNotFoundException extends RuntimeException
{
    public static function withId(string $id): self
    {
        return new self("Vendor not found: {$id}");
    }
}
