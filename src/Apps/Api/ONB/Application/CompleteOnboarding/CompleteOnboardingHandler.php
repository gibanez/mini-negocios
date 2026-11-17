<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ONB\Application\CompleteOnboarding;

use Gaby\Apps\Api\ONB\Domain\Exception\OnboardingAlreadyCompletedException;
use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;
use Gaby\Apps\Api\Store\Domain\Exception\StoreNotFoundException;

final class CompleteOnboardingHandler
{
    /** @var StoreRepositoryInterface */
    private $stores;

    public function __construct(StoreRepositoryInterface $stores)
    {
        $this->stores = $stores;
    }

    public function handle(CompleteOnboardingCommand $cmd): void
    {
        $store = $this->stores->findById($cmd->storeId);
        if ($store === null) {
            throw StoreNotFoundException::withId($cmd->storeId);
        }

        if ($store->onboardingCompleted()) {
            throw OnboardingAlreadyCompletedException::forStore($cmd->storeId);
        }

        $store->completeOnboarding();
        $this->stores->save($store);
    }
}
