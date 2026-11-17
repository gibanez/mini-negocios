<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\AdjustStock;

use Gaby\Apps\Api\CAT\Domain\Exception\NegativeStockException;
use Gaby\Apps\Api\CAT\Domain\Exception\ProductNotFoundException;
use Gaby\Apps\Api\CAT\Domain\StockAdjustment;
use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;

final class AdjustStockHandler
{
    /** @var StoreCatalogRepositoryInterface */ private $catalog;
    public function __construct(StoreCatalogRepositoryInterface $catalog) { $this->catalog = $catalog; }

    public function handle(AdjustStockCommand $command): StockAdjustment
    {
        $product = $this->catalog->findProductById($command->productId);
        if ($product === null || $product->storeId() !== $command->storeId) {
            throw ProductNotFoundException::withId($command->productId);
        }
        if ($command->newStock < 0) {
            throw NegativeStockException::create();
        }
        $adjustment = StockAdjustment::create(
            $product->id(), $command->storeId,
            $product->stock(), $command->newStock,
            $command->reason, $command->note, $command->responsibleName
        );
        $product->setStock($command->newStock);
        $this->catalog->saveProduct($product);
        $this->catalog->saveStockAdjustment($adjustment);
        return $adjustment;
    }
}
