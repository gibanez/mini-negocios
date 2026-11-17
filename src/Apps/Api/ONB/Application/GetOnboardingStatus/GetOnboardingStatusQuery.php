<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ONB\Application\GetOnboardingStatus;

final class GetOnboardingStatusQuery
{
    /** @var string */
    public $storeId;

    public function __construct(string $storeId)
    {
        $this->storeId = $storeId;
    }
}
