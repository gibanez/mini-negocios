<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Domain\Exception;
use RuntimeException;
final class MovementAlreadyCancelledException extends RuntimeException
{
    public static function create(): self { return new self('Movement is already cancelled.'); }
}
