<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Infrastructure\Persistence;

use Gaby\Apps\Api\FIA\Domain\CreditAccount;
use Gaby\Apps\Api\FIA\Domain\CreditAccountRepositoryInterface;
use Gaby\Apps\Api\FIA\Domain\CreditMovement;
use PDO;

final class SqliteCreditAccountRepository implements CreditAccountRepositoryInterface
{
    /** @var PDO */ private $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function findById(string $id): ?CreditAccount
    {
        $stmt = $this->pdo->prepare('SELECT * FROM credit_accounts WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(); return $row ? $this->hydrate($row) : null;
    }

    public function findByClientAndStore(string $clientId, string $storeId): ?CreditAccount
    {
        $stmt = $this->pdo->prepare('SELECT * FROM credit_accounts WHERE client_id = :c AND store_id = :s');
        $stmt->execute(['c' => $clientId, 's' => $storeId]);
        $row = $stmt->fetch(); return $row ? $this->hydrate($row) : null;
    }

    public function findByPublicToken(string $token): ?CreditAccount
    {
        $stmt = $this->pdo->prepare('SELECT * FROM credit_accounts WHERE public_token = :t');
        $stmt->execute(['t' => $token]);
        $row = $stmt->fetch(); return $row ? $this->hydrate($row) : null;
    }

    public function findByStoreId(string $storeId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM credit_accounts WHERE store_id = :s ORDER BY rowid ASC');
        $stmt->execute(['s' => $storeId]);
        return array_map([$this, 'hydrate'], $stmt->fetchAll());
    }

    public function save(CreditAccount $account): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO credit_accounts (id, store_id, client_id, public_token, status, created_at)
             VALUES (:id,:store_id,:client_id,:token,:status,:created_at)
             ON CONFLICT(id) DO UPDATE SET public_token=excluded.public_token, status=excluded.status'
        );
        $stmt->execute([
            'id'=>$account->id(),'store_id'=>$account->storeId(),'client_id'=>$account->clientId(),
            'token'=>$account->publicToken(),'status'=>$account->status(),'created_at'=>$account->createdAt(),
        ]);
    }

    public function saveMovement(CreditMovement $m): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO credit_movements (id, credit_account_id, store_id, type, amount, sale_id, payment_method, notes, status, cancelled_at, cancel_reason, cancelled_by, created_at)
             VALUES (:id,:account_id,:store_id,:type,:amount,:sale_id,:pm,:notes,:status,:cancelled_at,:cancel_reason,:cancelled_by,:created_at)
             ON CONFLICT(id) DO UPDATE SET status=excluded.status, cancelled_at=excluded.cancelled_at,
             cancel_reason=excluded.cancel_reason, cancelled_by=excluded.cancelled_by'
        );
        $stmt->execute([
            'id'=>$m->id(),'account_id'=>$m->creditAccountId(),'store_id'=>$m->storeId(),
            'type'=>$m->type(),'amount'=>$m->amount(),'sale_id'=>$m->saleId(),
            'pm'=>$m->paymentMethod(),'notes'=>$m->notes(),'status'=>$m->status(),
            'cancelled_at'=>$m->cancelledAt(),'cancel_reason'=>$m->cancelReason(),
            'cancelled_by'=>$m->cancelledBy(),'created_at'=>$m->createdAt(),
        ]);
    }

    public function findMovementById(string $id): ?CreditMovement
    {
        $stmt = $this->pdo->prepare('SELECT * FROM credit_movements WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(); return $row ? $this->hydrateMovement($row) : null;
    }

    public function findMovementBySaleId(string $saleId): ?CreditMovement
    {
        $stmt = $this->pdo->prepare('SELECT * FROM credit_movements WHERE sale_id = :id LIMIT 1');
        $stmt->execute(['id' => $saleId]);
        $row = $stmt->fetch(); return $row ? $this->hydrateMovement($row) : null;
    }

    public function findMovementsByAccountId(string $accountId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM credit_movements WHERE credit_account_id = :id ORDER BY created_at DESC');
        $stmt->execute(['id' => $accountId]);
        return array_map([$this, 'hydrateMovement'], $stmt->fetchAll());
    }

    public function getBalance(string $accountId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                COALESCE(SUM(CASE WHEN type='purchase' AND status='active' THEN amount ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN type='payment' AND status='active' THEN amount ELSE 0 END), 0)
             FROM credit_movements WHERE credit_account_id = :id"
        );
        $stmt->execute(['id' => $accountId]);
        return (int) $stmt->fetchColumn();
    }

    /** @param array<string,mixed> $row */
    private function hydrate(array $row): CreditAccount
    {
        return CreditAccount::fromPersistence(
            (string)$row['id'],(string)$row['store_id'],(string)$row['client_id'],
            (string)$row['public_token'],(string)$row['status'],(string)$row['created_at']
        );
    }

    /** @param array<string,mixed> $row */
    private function hydrateMovement(array $row): CreditMovement
    {
        return CreditMovement::fromPersistence(
            (string)$row['id'],(string)$row['credit_account_id'],(string)$row['store_id'],
            (string)$row['type'],(int)$row['amount'],
            isset($row['sale_id'])?(string)$row['sale_id']:null,
            isset($row['payment_method'])?(string)$row['payment_method']:null,
            isset($row['notes'])?(string)$row['notes']:null,
            (string)$row['status'],
            isset($row['cancelled_at'])?(string)$row['cancelled_at']:null,
            isset($row['cancel_reason'])?(string)$row['cancel_reason']:null,
            isset($row['cancelled_by'])?(string)$row['cancelled_by']:null,
            (string)$row['created_at']
        );
    }
}
