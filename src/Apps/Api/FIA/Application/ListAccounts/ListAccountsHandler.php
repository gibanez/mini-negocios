<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Application\ListAccounts;
use Gaby\Apps\Api\FIA\Domain\CreditAccountRepositoryInterface;
final class ListAccountsHandler
{
    /** @var CreditAccountRepositoryInterface */ private $repo;
    public function __construct(CreditAccountRepositoryInterface $repo) { $this->repo = $repo; }
    /** @return array<int,array<string,mixed>> */
    public function handle(ListAccountsQuery $q): array
    {
        $accounts = $this->repo->findByStoreId($q->storeId);
        return array_map(function ($a) {
            $data = $a->toArray();
            $data['balance'] = $this->repo->getBalance($a->id());
            return $data;
        }, $accounts);
    }
}
