<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ONB\Application\SetStoreTheme;

use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;
use Gaby\Apps\Api\Store\Domain\Exception\StoreNotFoundException;

final class SetStoreThemeHandler
{
    private const VALID_THEMES = ['verde', 'clasico', 'pizarra', 'zafiro', 'vino', 'medianoche', 'coral', 'aguamarina', 'cielo', 'ambar', 'rosa', 'carbon'];

    /** @var StoreRepositoryInterface */
    private $stores;

    public function __construct(StoreRepositoryInterface $stores)
    {
        $this->stores = $stores;
    }

    public function handle(SetStoreThemeCommand $cmd): void
    {
        if (!in_array($cmd->theme, self::VALID_THEMES, true)) {
            throw new \InvalidArgumentException("Invalid theme: {$cmd->theme}");
        }

        $store = $this->stores->findById($cmd->storeId);
        if ($store === null) {
            throw StoreNotFoundException::withId($cmd->storeId);
        }

        $store->setTheme($cmd->theme);
        $this->stores->save($store);
    }
}
