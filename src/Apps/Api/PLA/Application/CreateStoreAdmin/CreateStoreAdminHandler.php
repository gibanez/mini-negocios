<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\CreateStoreAdmin;

use Gaby\Apps\Api\ACC\Domain\Exception\EmailAlreadyInUseException;
use Gaby\Apps\Api\Admin\Domain\Admin;
use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;

final class CreateStoreAdminHandler
{
    /** @var AdminRepositoryInterface */
    private $admins;

    public function __construct(AdminRepositoryInterface $admins)
    {
        $this->admins = $admins;
    }

    /** @return array<string,mixed> */
    public function handle(CreateStoreAdminCommand $c): array
    {
        if ($this->admins->findByEmail($c->email) !== null) {
            throw EmailAlreadyInUseException::withEmail($c->email);
        }

        $admin = $c->role === Admin::ROLE_VENDOR
            ? Admin::createVendor($c->name, $c->email, $c->password, $c->storeId)
            : Admin::createOwner($c->name, $c->email, $c->password, $c->storeId);

        $this->admins->save($admin);
        return $admin->toArray();
    }
}
