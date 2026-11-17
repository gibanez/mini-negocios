<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CAT\Application\ListCatalog;

use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;

final class ListCatalogHandler
{
    /** @var StoreCatalogRepositoryInterface */
    private $catalog;

    public function __construct(StoreCatalogRepositoryInterface $catalog)
    {
        $this->catalog = $catalog;
    }

    /** @return array<string,mixed> */
    public function handle(ListCatalogQuery $query): array
    {
        $categories = $this->catalog->findCategoriesByStoreId($query->storeId);

        $result = [];
        foreach ($categories as $cat) {
            $products = $this->catalog->findProductsByCategoryId($cat->id());
            $entry = $cat->toArray();
            $entry['products'] = array_map(fn($p) => $p->toArray(), $products);
            $result[] = $entry;
        }

        return $result;
    }
}
