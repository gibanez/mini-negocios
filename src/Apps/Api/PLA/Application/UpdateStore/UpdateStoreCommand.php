<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\UpdateStore;

final class UpdateStoreCommand
{
    /** @var string */
    public $storeId;

    /** @var string */
    public $name;

    public function __construct(string $storeId, string $name)
    {
        $this->storeId = $storeId;
        $this->name    = $name;
    }
}
