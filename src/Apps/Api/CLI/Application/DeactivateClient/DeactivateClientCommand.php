<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CLI\Application\DeactivateClient;

final class DeactivateClientCommand
{
    /** @var string */ public $storeId;
    /** @var string */ public $clientId;

    public function __construct(string $storeId, string $clientId)
    {
        $this->storeId  = $storeId;
        $this->clientId = $clientId;
    }
}
