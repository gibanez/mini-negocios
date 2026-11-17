<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\DeactivateAdmin;

use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;

final class DeactivateAdminHandler
{
    /** @var AdminRepositoryInterface */
    private $admins;

    public function __construct(AdminRepositoryInterface $admins)
    {
        $this->admins = $admins;
    }

    public function handle(DeactivateAdminCommand $c): void
    {
        $admin = $this->admins->findById($c->adminId);
        if ($admin === null) {
            return;
        }
        $admin->deactivate();
        $this->admins->save($admin);
    }
}
