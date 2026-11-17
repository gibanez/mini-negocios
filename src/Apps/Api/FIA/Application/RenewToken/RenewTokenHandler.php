<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Application\RenewToken;
use Gaby\Apps\Api\FIA\Domain\CreditAccount;
use Gaby\Apps\Api\FIA\Domain\CreditAccountRepositoryInterface;
final class RenewTokenHandler
{
    /** @var CreditAccountRepositoryInterface */ private $repo;
    public function __construct(CreditAccountRepositoryInterface $repo) { $this->repo = $repo; }
    public function handle(RenewTokenCommand $cmd): string
    {
        $account = $this->repo->findByClientAndStore($cmd->clientId, $cmd->storeId);
        if ($account === null) {
            $account = CreditAccount::create($cmd->storeId, $cmd->clientId);
        }
        $account->renewToken();
        $this->repo->save($account);
        return $account->publicToken();
    }
}
