<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Admin\Application\UpdateUser;

use Gaby\Apps\Api\Admin\Domain\Admin;
use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;

final class UpdateUserHandler
{
    /** @var AdminRepositoryInterface */
    private $repo;

    public function __construct(AdminRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /** @return array<string,mixed> */
    public function handle(UpdateUserCommand $cmd): array
    {
        $admin = $this->repo->findById($cmd->userId);
        if ($admin === null || $admin->storeId() !== $cmd->storeId) {
            throw new \RuntimeException("User not found.");
        }

        if ($cmd->name !== null && $cmd->name !== '') {
            $admin->rename($cmd->name);
        }

        if ($cmd->status === Admin::STATUS_INACTIVE) {
            if ($admin->isOwner() && $this->repo->countActiveOwnersByStoreId($cmd->storeId) <= 1) {
                throw new \DomainException("Cannot deactivate the last active owner.");
            }
            $admin->deactivate();
        } elseif ($cmd->status === Admin::STATUS_ACTIVE) {
            $admin->activate();
        }

        $this->repo->save($admin);
        return $admin->toArray();
    }
}
