<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\GetStockHistory;

use Gaby\Apps\Api\CAT\Domain\Exception\ProductNotFoundException;
use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;

final class GetStockHistoryHandler
{
    /** @var StoreCatalogRepositoryInterface */ private $catalog;
    public function __construct(StoreCatalogRepositoryInterface $catalog) { $this->catalog = $catalog; }

    /** @return array<int,array<string,mixed>> */
    public function handle(GetStockHistoryQuery $query): array
    {
        $product = $this->catalog->findProductById($query->productId);
        if ($product === null || $product->storeId() !== $query->storeId) {
            throw ProductNotFoundException::withId($query->productId);
        }
        $history = $this->catalog->findStockHistoryByProductId($query->productId);
        return array_map(function ($adj) { return $adj->toArray(); }, $history);
    }
}
