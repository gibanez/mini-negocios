<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Admin\Application\ListUsers;

final class ListUsersQuery
{
    /** @var string */
    public $storeId;

    public function __construct(string $storeId)
    {
        $this->storeId = $storeId;
    }
}
