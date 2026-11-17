<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ACC\Domain\Exception;

use RuntimeException;

final class EmailAlreadyInUseException extends RuntimeException
{
    public static function withEmail(string $email): self
    {
        return new self("Email already in use: {$email}");
    }
}
