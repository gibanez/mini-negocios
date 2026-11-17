<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Admin\Application\DeleteUser;

final class DeleteUserCommand
{
    /** @var string */ public $storeId;
    /** @var string */ public $userId;

    public function __construct(string $storeId, string $userId)
    {
        $this->storeId = $storeId;
        $this->userId  = $userId;
    }
}
