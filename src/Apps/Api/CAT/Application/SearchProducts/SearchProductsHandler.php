<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\SearchProducts;

use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;

final class SearchProductsHandler
{
    /** @var StoreCatalogRepositoryInterface */ private $catalog;
    public function __construct(StoreCatalogRepositoryInterface $catalog) { $this->catalog = $catalog; }

    /** @return array<int,array<string,mixed>> */
    public function handle(SearchProductsQuery $query): array
    {
        $products = $this->catalog->findProductsByStoreId($query->storeId, $query->search, 'active');
        return array_map(function ($p) { return $p->toArray(); }, $products);
    }
}
