<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CLI\Domain\Exception;

use RuntimeException;

final class ClientNotFoundException extends RuntimeException
{
    public static function withId(string $id): self { return new self("Client not found: {$id}"); }
}
