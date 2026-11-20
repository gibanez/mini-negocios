<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\ImpersonateAdmin;

final class ImpersonateAdminCommand
{
    /** @var string */ public $adminId;

    public function __construct(string $adminId)
    {
        $this->adminId = $adminId;
    }
}
