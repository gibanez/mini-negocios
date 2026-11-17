<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Application\AddDebt;
use Gaby\Apps\Api\FIA\Domain\CreditAccount;
use Gaby\Apps\Api\FIA\Domain\CreditAccountRepositoryInterface;
use Gaby\Apps\Api\FIA\Domain\CreditMovement;
final class AddDebtHandler
{
    /** @var CreditAccountRepositoryInterface */ private $repo;
    public function __construct(CreditAccountRepositoryInterface $repo) { $this->repo = $repo; }
    public function handle(AddDebtCommand $cmd): CreditMovement
    {
        $account = $this->repo->findByClientAndStore($cmd->clientId, $cmd->storeId);
        if ($account === null) {
            $account = CreditAccount::create($cmd->storeId, $cmd->clientId);
            $this->repo->save($account);
        }
        $movement = CreditMovement::createDebt($account->id(), $cmd->storeId, $cmd->amount, $cmd->saleId, $cmd->notes);
        $this->repo->saveMovement($movement);
        return $movement;
    }
}
