<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\FIA\Application\GetAccount;

use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;
use Gaby\Apps\Api\FIA\Domain\CreditAccount;
use Gaby\Apps\Api\FIA\Domain\CreditAccountRepositoryInterface;
use Gaby\Apps\Api\FIA\Domain\CreditMovement;
use Gaby\Apps\Api\VEN\Domain\SaleRepositoryInterface;

final class GetAccountHandler
{
    /** @var CreditAccountRepositoryInterface */ private $repo;
    /** @var SaleRepositoryInterface */          private $saleRepo;
    /** @var StoreCatalogRepositoryInterface */  private $catalog;

    public function __construct(
        CreditAccountRepositoryInterface $repo,
        SaleRepositoryInterface $saleRepo,
        StoreCatalogRepositoryInterface $catalog
    ) {
        $this->repo     = $repo;
        $this->saleRepo = $saleRepo;
        $this->catalog  = $catalog;
    }

    /** @return array<string,mixed> */
    public function handle(GetAccountQuery $q): array
    {
        $account = $this->repo->findByClientAndStore($q->clientId, $q->storeId);
        if ($account === null) {
            $account = CreditAccount::create($q->storeId, $q->clientId);
            $this->repo->save($account);
        }

        $movements = $this->repo->findMovementsByAccountId($account->id());

        // Recalculate purchase movements linked to a sale using current prices
        $balance = 0;
        $movementsData = [];
        foreach ($movements as $m) {
            $arr = $m->toArray();
            if ($m->status() !== CreditMovement::STATUS_ACTIVE) {
                $movementsData[] = $arr;
                continue;
            }
            if ($m->type() === CreditMovement::TYPE_PURCHASE && $m->saleId() !== null) {
                $items = $this->saleRepo->findItemsBySaleId($m->saleId());
                $recalculated = 0;
                foreach ($items as $item) {
                    $productId = $item->storeProductId();
                    if ($productId !== null) {
                        $product = $this->catalog->findProductById($productId);
                        $price   = ($product !== null && $product->price() !== null) ? $product->price() : $item->unitPrice();
                    } else {
                        $price = $item->unitPrice();
                    }
                    $recalculated += (int) round($item->quantity() * $price / 1000);
                }
                $arr['amount']       = $recalculated;
                $arr['amountOrig']   = $m->amount(); // precio original por si se necesita mostrar
                $balance += $recalculated;
            } elseif ($m->type() === CreditMovement::TYPE_PAYMENT) {
                $balance -= $m->amount();
            } else {
                // deuda manual sin venta: usar monto guardado
                $balance += $m->amount();
            }
            $movementsData[] = $arr;
        }

        $data = $account->toArray();
        $data['balance']   = $balance;
        $data['movements'] = $movementsData;
        return $data;
    }
}
