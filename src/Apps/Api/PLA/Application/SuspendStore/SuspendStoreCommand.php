<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\SuspendStore;

final class SuspendStoreCommand
{
    /** @var string */
    public $storeId;

    /** @var string */
    public $reason;

    public function __construct(string $storeId, string $reason)
    {
        $this->storeId = $storeId;
        $this->reason  = $reason;
    }
}
