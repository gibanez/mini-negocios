<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Domain\Exception;
use RuntimeException;
final class MovementNotFoundException extends RuntimeException
{
    public static function withId(string $id): self { return new self("Movement not found: {$id}"); }
}
