<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Domain\Exception;
use RuntimeException;
final class CategoryNameAlreadyUsedException extends RuntimeException
{
    public static function withName(string $name): self { return new self("Category name already used: {$name}"); }
}
