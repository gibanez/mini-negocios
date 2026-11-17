<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ONB\Application\CompleteOnboarding;

final class CompleteOnboardingCommand
{
    /** @var string */
    public $storeId;

    public function __construct(string $storeId)
    {
        $this->storeId = $storeId;
    }
}
