<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Admin\Application\DeleteUser;

use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;

final class DeleteUserHandler
{
    /** @var AdminRepositoryInterface */
    private $repo;

    public function __construct(AdminRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function handle(DeleteUserCommand $cmd): void
    {
        $admin = $this->repo->findById($cmd->userId);
        if ($admin === null || $admin->storeId() !== $cmd->storeId) {
            throw new \RuntimeException("User not found.");
        }

        if ($admin->isOwner() && $this->repo->countActiveOwnersByStoreId($cmd->storeId) <= 1) {
            throw new \DomainException("Cannot deactivate the last active owner.");
        }

        $admin->deactivate();
        $this->repo->save($admin);
    }
}
