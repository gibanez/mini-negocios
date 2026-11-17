<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Admin\Application\ListUsers;

use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;

final class ListUsersHandler
{
    /** @var AdminRepositoryInterface */
    private $repo;

    public function __construct(AdminRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /** @return array<int,array<string,mixed>> */
    public function handle(ListUsersQuery $q): array
    {
        $admins = $this->repo->findAllByStoreId($q->storeId);
        return array_map(function ($a) { return $a->toArray(); }, $admins);
    }
}
