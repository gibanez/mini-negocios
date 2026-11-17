<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\GetStore;

final class GetStoreQuery
{
    /** @var string */
    public $storeId;

    public function __construct(string $storeId)
    {
        $this->storeId = $storeId;
    }
}
