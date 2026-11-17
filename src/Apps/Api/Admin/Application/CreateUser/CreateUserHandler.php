<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Admin\Application\CreateUser;

use Gaby\Apps\Api\Admin\Domain\Admin;
use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;

final class CreateUserHandler
{
    /** @var AdminRepositoryInterface */
    private $repo;

    public function __construct(AdminRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /** @return array<string,mixed> */
    public function handle(CreateUserCommand $cmd): array
    {
        if (!in_array($cmd->role, [Admin::ROLE_OWNER, Admin::ROLE_VENDOR], true)) {
            throw new \InvalidArgumentException("role must be 'owner' or 'vendor'.");
        }

        if ($this->repo->findByEmail($cmd->email) !== null) {
            throw new \DomainException("Email already in use.");
        }

        if ($cmd->role === Admin::ROLE_OWNER) {
            $admin = Admin::createOwner($cmd->name, $cmd->email, $cmd->password, $cmd->storeId);
        } else {
            $admin = Admin::createVendor($cmd->name, $cmd->email, $cmd->password, $cmd->storeId);
        }

        $this->repo->save($admin);
        return $admin->toArray();
    }
}
