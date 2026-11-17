<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Personal\Application\ListPersonal;

use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;

final class ListPersonalHandler
{
    /** @var AdminRepositoryInterface */
    private $admins;

    public function __construct(AdminRepositoryInterface $admins)
    {
        $this->admins = $admins;
    }

    /** @return array<int, array<string, mixed>> */
    public function handle(ListPersonalQuery $query): array
    {
        $members = $this->admins->findAllByStoreId($query->storeId);

        return array_values(array_map(function ($m): array {
            return [
                'id'     => $m->id(),
                'name'   => $m->name(),
                'email'  => $m->email(),
                'role'   => $m->role(),
                'status' => $m->status(),
            ];
        }, $members));
    }
}
