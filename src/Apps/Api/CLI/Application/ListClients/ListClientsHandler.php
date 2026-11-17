<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CLI\Application\ListClients;

use Gaby\Apps\Api\CLI\Domain\ClientRepositoryInterface;

final class ListClientsHandler
{
    /** @var ClientRepositoryInterface */ private $repo;

    public function __construct(ClientRepositoryInterface $repo) { $this->repo = $repo; }

    /** @return array<int,array<string,mixed>> */
    public function handle(ListClientsQuery $q): array
    {
        return array_map(function ($c) { return $c->toArray(); }, $this->repo->findByStoreId($q->storeId, $q->search));
    }
}
