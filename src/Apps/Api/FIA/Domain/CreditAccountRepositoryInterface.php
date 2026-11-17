<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Domain;

interface CreditAccountRepositoryInterface
{
    public function findById(string $id): ?CreditAccount;
    public function findByClientAndStore(string $clientId, string $storeId): ?CreditAccount;
    public function findByPublicToken(string $token): ?CreditAccount;
    /** @return CreditAccount[] */
    public function findByStoreId(string $storeId): array;
    public function save(CreditAccount $account): void;

    public function saveMovement(CreditMovement $movement): void;
    public function findMovementById(string $id): ?CreditMovement;
    public function findMovementBySaleId(string $saleId): ?CreditMovement;
    /** @return CreditMovement[] */
    public function findMovementsByAccountId(string $accountId): array;
    public function getBalance(string $accountId): int;
}
