<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Application\CreateSale;

use Gaby\Apps\Api\CAT\Domain\Exception\ProductNotFoundException;
use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;
use Gaby\Apps\Api\FIA\Domain\CreditAccount;
use Gaby\Apps\Api\FIA\Domain\CreditAccountRepositoryInterface;
use Gaby\Apps\Api\FIA\Domain\CreditMovement;
use Gaby\Apps\Api\VEN\Domain\Sale;
use Gaby\Apps\Api\VEN\Domain\SaleItem;
use Gaby\Apps\Api\VEN\Domain\SaleRepositoryInterface;

final class CreateSaleHandler
{
    /** @var SaleRepositoryInterface */           private $saleRepo;
    /** @var StoreCatalogRepositoryInterface */   private $catalog;
    /** @var CreditAccountRepositoryInterface */  private $creditRepo;

    public function __construct(
        SaleRepositoryInterface $saleRepo,
        StoreCatalogRepositoryInterface $catalog,
        CreditAccountRepositoryInterface $creditRepo
    ) {
        $this->saleRepo   = $saleRepo;
        $this->catalog    = $catalog;
        $this->creditRepo = $creditRepo;
    }

    public function handle(CreateSaleCommand $cmd): Sale
    {
        // Phase 1: validate and collect resolved items + total
        $resolvedItems = [];
        $total = 0;
        foreach ($cmd->items as $raw) {
            $productId   = isset($raw['productId']) && $raw['productId'] !== '' ? (string)$raw['productId'] : null;
            $quantity    = (int)($raw['quantity']    ?? 0);
            $unitPrice   = (int)($raw['unitPrice']   ?? 0);
            $productName = (string)($raw['productName'] ?? '');

            if ($productId !== null) {
                $product = $this->catalog->findProductById($productId);
                if ($product === null || $product->storeId() !== $cmd->storeId) {
                    throw ProductNotFoundException::withId($productId);
                }
                $productName = $product->name();
            }
            $subtotal = (int) round($quantity * $unitPrice / 1000);
            $total += $subtotal;
            $resolvedItems[] = [
                'productId'   => $productId,
                'productName' => $productName,
                'quantity'    => $quantity,
                'unitPrice'   => $unitPrice,
            ];
        }

        // Phase 2: create sale with correct total
        $sale = Sale::create($cmd->storeId, $cmd->clientId, $total, $cmd->paymentMethod,
            $cmd->responsibleId, $cmd->responsibleName, $cmd->notes);

        // Phase 3: create items and update stock
        $saleItems = [];
        foreach ($resolvedItems as $r) {
            $item = SaleItem::create($sale->id(), $r['productId'], $r['productName'], $r['quantity'], $r['unitPrice']);
            $saleItems[] = $item;
            if ($r['productId'] !== null) {
                $product = $this->catalog->findProductById($r['productId']);
                if ($product !== null) {
                    $product->setStock(max(0, $product->stock() - $r['quantity']));
                    $this->catalog->saveProduct($product);
                }
            }
        }

        $this->saleRepo->save($sale);
        $this->saleRepo->saveItems($saleItems);
        $sale->setItems($saleItems);

        // Si es fiado, registrar la compra en la cuenta corriente del cliente
        if ($cmd->paymentMethod === 'fiado' && $cmd->clientId !== null) {
            $account = $this->creditRepo->findByClientAndStore($cmd->clientId, $cmd->storeId);
            if ($account === null) {
                $account = CreditAccount::create($cmd->storeId, $cmd->clientId);
                $this->creditRepo->save($account);
            }
            $movement = CreditMovement::createDebt($account->id(), $cmd->storeId, $total, $sale->id(), null);
            $this->creditRepo->saveMovement($movement);
        }

        return $sale;
    }
}
