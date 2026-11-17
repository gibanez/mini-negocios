<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\CreateProduct;

use Gaby\Apps\Api\CAT\Domain\Exception\CategoryNotFoundException;
use Gaby\Apps\Api\CAT\Domain\StoreProduct;
use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;

final class CreateProductHandler
{
    /** @var StoreCatalogRepositoryInterface */ private $catalog;
    public function __construct(StoreCatalogRepositoryInterface $catalog) { $this->catalog = $catalog; }

    public function handle(CreateProductCommand $command): StoreProduct
    {
        $category = $this->catalog->findCategoryById($command->categoryId);
        if ($category === null || $category->storeId() !== $command->storeId) {
            throw CategoryNotFoundException::withId($command->categoryId);
        }
        $product = StoreProduct::createFromTemplate($command->storeId, $command->categoryId, $command->name, $command->unit);
        $product->setBrand($command->brand);
        $product->setPrice($command->price);
        $product->setStock($command->initialStock);
        $this->catalog->saveProduct($product);
        return $product;
    }
}
