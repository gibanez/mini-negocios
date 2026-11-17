<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ONB\Application\GetOnboardingStatus;

use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;
use Gaby\Apps\Api\Store\Domain\Exception\StoreNotFoundException;

final class GetOnboardingStatusHandler
{
    /** @var StoreRepositoryInterface */
    private $stores;

    public function __construct(StoreRepositoryInterface $stores)
    {
        $this->stores = $stores;
    }

    /** @return array<string,mixed> */
    public function handle(GetOnboardingStatusQuery $query): array
    {
        $store = $this->stores->findById($query->storeId);
        if ($store === null) {
            throw StoreNotFoundException::withId($query->storeId);
        }

        return [
            'completed' => $store->onboardingCompleted(),
            'name'      => $store->name(),
            'industry'  => $store->industry(),
            'theme'     => $store->theme(),
            'logoPath'  => $store->logoPath(),
        ];
    }
}
