<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Domain\Exception;
use RuntimeException;
final class CreditAccountNotFoundException extends RuntimeException
{
    public static function forClient(string $clientId): self { return new self("No credit account for client: {$clientId}"); }
    public static function forToken(string $token): self { return new self("Credit account not found for token: {$token}"); }
}
