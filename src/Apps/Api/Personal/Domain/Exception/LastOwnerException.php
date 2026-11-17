<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Personal\Domain\Exception;

use RuntimeException;

final class LastOwnerException extends RuntimeException
{
    public static function create(): self
    {
        return new self('Cannot deactivate the last active owner of the store.');
    }
}
