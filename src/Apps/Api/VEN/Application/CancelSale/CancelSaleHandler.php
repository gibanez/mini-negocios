<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\VEN\Application\CancelSale;

use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;
use Gaby\Apps\Api\FIA\Domain\CreditAccountRepositoryInterface;
use Gaby\Apps\Api\VEN\Domain\Exception\SaleAlreadyCancelledException;
use Gaby\Apps\Api\VEN\Domain\Exception\SaleCancellationNotAllowedException;
use Gaby\Apps\Api\VEN\Domain\Exception\SaleNotFoundException;
use Gaby\Apps\Api\VEN\Domain\SaleRepositoryInterface;

final class CancelSaleHandler
{
    /** @var SaleRepositoryInterface */          private $saleRepo;
    /** @var StoreCatalogRepositoryInterface */  private $catalog;
    /** @var CreditAccountRepositoryInterface */ private $creditRepo;

    public function __construct(
        SaleRepositoryInterface $saleRepo,
        StoreCatalogRepositoryInterface $catalog,
        CreditAccountRepositoryInterface $creditRepo
    ) {
        $this->saleRepo   = $saleRepo;
        $this->catalog    = $catalog;
        $this->creditRepo = $creditRepo;
    }

    public function handle(CancelSaleCommand $cmd): void
    {
        $sale = $this->saleRepo->findById($cmd->saleId);
        if ($sale === null || $sale->storeId() !== $cmd->storeId) {
            throw SaleNotFoundException::withId($cmd->saleId);
        }
        if (!$sale->isCompleted()) {
            throw SaleAlreadyCancelledException::create();
        }
        $saleDate = substr($sale->createdAt(), 0, 10);
        if ($saleDate !== $cmd->today) {
            throw SaleCancellationNotAllowedException::notSameDay();
        }
        // restore stock
        foreach ($sale->items() as $item) {
            if ($item->storeProductId() !== null) {
                $product = $this->catalog->findProductById($item->storeProductId());
                if ($product !== null) {
                    $product->setStock($product->stock() + $item->quantity());
                    $this->catalog->saveProduct($product);
                }
            }
        }
        $sale->cancel($cmd->reason, $cmd->cancelledBy);
        $this->saleRepo->save($sale);

        // Si era fiado, cancelar el movimiento de la cuenta corriente
        if ($sale->paymentMethod() === 'fiado') {
            $movement = $this->creditRepo->findMovementBySaleId($sale->id());
            if ($movement !== null && $movement->isActive()) {
                $movement->cancel($cmd->reason, $cmd->cancelledBy);
                $this->creditRepo->saveMovement($movement);
            }
        }
    }
}
