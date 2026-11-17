<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ONB\Domain\Exception;

use RuntimeException;

final class OnboardingAlreadyCompletedException extends RuntimeException
{
    public static function forStore(string $storeId): self
    {
        return new self("Onboarding already completed for store {$storeId}.");
    }
}
