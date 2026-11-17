<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CLI\Application\ListClients;

final class ListClientsQuery
{
    /** @var string */      public $storeId;
    /** @var string|null */ public $search;

    public function __construct(string $storeId, ?string $search)
    {
        $this->storeId = $storeId;
        $this->search  = $search;
    }
}
