<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Domain\Exception;
use RuntimeException;
final class ProductNotFoundException extends RuntimeException
{
    public static function withId(string $id): self { return new self("Product not found: {$id}"); }
}
