<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Admin\Domain;

interface AdminRepositoryInterface
{
    public function findById(string $id): ?Admin;

    public function findByEmail(string $email): ?Admin;

    /** @return Admin[] */
    public function findAllByStoreId(string $storeId): array;

    public function countActiveOwnersByStoreId(string $storeId): int;

    public function save(Admin $admin): void;

    public function deleteById(string $id): void;
}
