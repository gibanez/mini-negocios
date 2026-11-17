<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Application\GetPublicSummary;

use Gaby\Apps\Api\CLI\Domain\ClientRepositoryInterface;
use Gaby\Apps\Api\FIA\Domain\CreditAccountRepositoryInterface;
use Gaby\Apps\Api\FIA\Domain\Exception\CreditAccountNotFoundException;
use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;

final class GetPublicSummaryHandler
{
    /** @var CreditAccountRepositoryInterface */ private $fiaRepo;
    /** @var ClientRepositoryInterface */        private $clientRepo;
    /** @var StoreRepositoryInterface */         private $storeRepo;

    public function __construct(
        CreditAccountRepositoryInterface $fiaRepo,
        ClientRepositoryInterface $clientRepo,
        StoreRepositoryInterface $storeRepo
    ) {
        $this->fiaRepo    = $fiaRepo;
        $this->clientRepo = $clientRepo;
        $this->storeRepo  = $storeRepo;
    }

    /** @return array<string,mixed> */
    public function handle(GetPublicSummaryQuery $q): array
    {
        $account = $this->fiaRepo->findByPublicToken($q->token);
        if ($account === null) {
            throw CreditAccountNotFoundException::forToken($q->token);
        }
        $client    = $this->clientRepo->findById($account->clientId());
        $store     = $this->storeRepo->findById($account->storeId());
        $balance   = $this->fiaRepo->getBalance($account->id());
        $movements = $this->fiaRepo->findMovementsByAccountId($account->id());

        return [
            'clientName' => $client ? $client->name() : 'Cliente',
            'storeName'  => $store  ? $store->name()  : '',
            'theme'      => $store  ? $store->theme()  : 'verde',
            'balance'    => $balance,
            'movements'  => array_map(function ($m) { return $m->toArray(); }, $movements),
        ];
    }
}
