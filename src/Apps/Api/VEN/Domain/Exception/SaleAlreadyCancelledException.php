<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Domain\Exception;

use RuntimeException;

final class SaleAlreadyCancelledException extends RuntimeException
{
    public static function create(): self { return new self('Sale is already cancelled.'); }
}
