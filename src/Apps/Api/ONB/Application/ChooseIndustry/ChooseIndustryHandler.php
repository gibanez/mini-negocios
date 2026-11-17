<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ONB\Application\ChooseIndustry;

use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;
use Gaby\Apps\Api\Store\Domain\Exception\StoreNotFoundException;
use Gaby\Apps\Api\Industry\Domain\IndustryRepositoryInterface;
use Gaby\Apps\Api\CAT\Domain\StoreCategory;
use Gaby\Apps\Api\CAT\Domain\StoreProduct;
use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;

final class ChooseIndustryHandler
{
    /** @var StoreRepositoryInterface */
    private $stores;
    /** @var IndustryRepositoryInterface */
    private $industries;
    /** @var StoreCatalogRepositoryInterface */
    private $catalog;

    public function __construct(
        StoreRepositoryInterface $stores,
        IndustryRepositoryInterface $industries,
        StoreCatalogRepositoryInterface $catalog
    ) {
        $this->stores     = $stores;
        $this->industries = $industries;
        $this->catalog    = $catalog;
    }

    /** @return array<string,int> */
    public function handle(ChooseIndustryCommand $cmd): array
    {
        $store = $this->stores->findById($cmd->storeId);
        if ($store === null) {
            throw StoreNotFoundException::withId($cmd->storeId);
        }

        $industry = $this->industries->findById($cmd->industryId);
        if ($industry === null) {
            throw new \InvalidArgumentException("Industry not found: {$cmd->industryId}");
        }

        $store->setIndustry($industry->name());
        $this->stores->save($store);

        $templateCategories = $this->industries->findCategoriesByIndustryId($cmd->industryId);
        $categoriesCreated = 0;
        $productsCreated   = 0;

        foreach ($templateCategories as $tmplCat) {
            $existing = $this->catalog->findCategoryByNameAndStoreId($tmplCat->name(), $cmd->storeId);
            if ($existing !== null) {
                $catId = $existing->id();
            } else {
                $newCat = StoreCategory::create($cmd->storeId, $tmplCat->name());
                $this->catalog->saveCategory($newCat);
                $catId = $newCat->id();
                $categoriesCreated++;
            }

            $templateProducts = $this->industries->findProductsByCategoryId($tmplCat->id());
            foreach ($templateProducts as $tmplProd) {
                $existingProd = $this->catalog->findProductByNameAndStoreId($tmplProd->name(), $cmd->storeId);
                if ($existingProd !== null) {
                    continue;
                }
                $newProd = StoreProduct::createFromTemplate(
                    $cmd->storeId,
                    $catId,
                    $tmplProd->name(),
                    $tmplProd->unit()
                );
                $this->catalog->saveProduct($newProd);
                $productsCreated++;
            }
        }

        return [
            'categories' => $categoriesCreated,
            'products'   => $productsCreated,
        ];
    }
}
