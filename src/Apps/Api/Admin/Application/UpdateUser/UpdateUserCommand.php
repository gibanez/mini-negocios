<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Admin\Application\UpdateUser;

final class UpdateUserCommand
{
    /** @var string */      public $storeId;
    /** @var string */      public $userId;
    /** @var string|null */ public $name;
    /** @var string|null */ public $status;

    public function __construct(string $storeId, string $userId, ?string $name, ?string $status)
    {
        $this->storeId = $storeId;
        $this->userId  = $userId;
        $this->name    = $name;
        $this->status  = $status;
    }
}
