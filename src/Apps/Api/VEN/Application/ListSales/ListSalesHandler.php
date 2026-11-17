<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Application\ListSales;

use Gaby\Apps\Api\CLI\Domain\ClientRepositoryInterface;
use Gaby\Apps\Api\VEN\Domain\SaleRepositoryInterface;

final class ListSalesHandler
{
    /** @var SaleRepositoryInterface */   private $repo;
    /** @var ClientRepositoryInterface */ private $clients;

    public function __construct(SaleRepositoryInterface $repo, ClientRepositoryInterface $clients)
    {
        $this->repo    = $repo;
        $this->clients = $clients;
    }

    /** @return array<int,array<string,mixed>> */
    public function handle(ListSalesQuery $q): array
    {
        $sales = $this->repo->findByStoreId($q->storeId, $q->dateFrom, $q->dateTo, $q->clientId);
        // Cache clients to avoid N+1
        $clientCache = [];
        return array_map(function ($s) use (&$clientCache) {
            $data = $s->toArray();
            if ($s->clientId() !== null) {
                if (!isset($clientCache[$s->clientId()])) {
                    $c = $this->clients->findById($s->clientId());
                    $clientCache[$s->clientId()] = $c ? $c->name() : null;
                }
                $data['clientName'] = $clientCache[$s->clientId()];
            }
            return $data;
        }, $sales);
    }
}
