<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\ListStoreAdmins;

use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;

final class ListStoreAdminsHandler
{
    /** @var AdminRepositoryInterface */
    private $admins;

    public function __construct(AdminRepositoryInterface $admins)
    {
        $this->admins = $admins;
    }

    /** @return array<int,array<string,mixed>> */
    public function handle(ListStoreAdminsQuery $q): array
    {
        return array_map(
            function ($a) { return $a->toArray(); },
            $this->admins->findAllByStoreId($q->storeId)
        );
    }
}
