<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CLI\Domain;

interface ClientRepositoryInterface
{
    public function findById(string $id): ?Client;
    /** @return Client[] */
    public function findByStoreId(string $storeId, ?string $search = null): array;
    public function save(Client $client): void;
}
