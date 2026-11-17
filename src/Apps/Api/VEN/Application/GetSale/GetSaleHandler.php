<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Application\GetSale;

use Gaby\Apps\Api\CLI\Domain\ClientRepositoryInterface;
use Gaby\Apps\Api\VEN\Domain\Exception\SaleNotFoundException;
use Gaby\Apps\Api\VEN\Domain\SaleRepositoryInterface;

final class GetSaleHandler
{
    /** @var SaleRepositoryInterface */   private $repo;
    /** @var ClientRepositoryInterface */ private $clients;

    public function __construct(SaleRepositoryInterface $repo, ClientRepositoryInterface $clients)
    {
        $this->repo    = $repo;
        $this->clients = $clients;
    }

    /** @return array<string,mixed> */
    public function handle(GetSaleQuery $q): array
    {
        $sale = $this->repo->findById($q->saleId);
        if ($sale === null || $sale->storeId() !== $q->storeId) {
            throw SaleNotFoundException::withId($q->saleId);
        }
        $data = $sale->toArray();
        if ($sale->clientId() !== null) {
            $client = $this->clients->findById($sale->clientId());
            $data['clientName'] = $client ? $client->name() : null;
        }
        return $data;
    }
}
