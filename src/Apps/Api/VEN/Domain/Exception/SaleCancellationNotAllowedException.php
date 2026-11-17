<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Domain\Exception;

use RuntimeException;

final class SaleCancellationNotAllowedException extends RuntimeException
{
    public static function notSameDay(): self { return new self('Sales can only be cancelled on the same day.'); }
}
