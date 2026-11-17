<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CLI\Application\CreateClient;

final class CreateClientCommand
{
    /** @var string */      public $storeId;
    /** @var string */      public $name;
    /** @var string|null */ public $nickname;
    /** @var string|null */ public $phone;
    /** @var string|null */ public $address;
    /** @var string|null */ public $notes;

    public function __construct(string $storeId, string $name, ?string $nickname,
        ?string $phone, ?string $address, ?string $notes)
    {
        $this->storeId   = $storeId;
        $this->name      = $name;
        $this->nickname  = $nickname;
        $this->phone     = $phone;
        $this->address   = $address;
        $this->notes     = $notes;
    }
}
