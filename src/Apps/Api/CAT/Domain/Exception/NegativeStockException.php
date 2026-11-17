<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Domain\Exception;
use RuntimeException;
final class NegativeStockException extends RuntimeException
{
    public static function create(): self { return new self('Stock cannot be negative.'); }
}
