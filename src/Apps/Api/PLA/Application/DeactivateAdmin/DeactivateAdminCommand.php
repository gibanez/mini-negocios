<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\DeactivateAdmin;

final class DeactivateAdminCommand
{
    /** @var string */
    public $adminId;

    public function __construct(string $adminId)
    {
        $this->adminId = $adminId;
    }
}
