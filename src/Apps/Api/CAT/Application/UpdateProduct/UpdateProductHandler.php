<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\UpdateProduct;

use Gaby\Apps\Api\CAT\Domain\Exception\CategoryNotFoundException;
use Gaby\Apps\Api\CAT\Domain\Exception\ProductNotFoundException;
use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;

final class UpdateProductHandler
{
    /** @var StoreCatalogRepositoryInterface */ private $catalog;
    public function __construct(StoreCatalogRepositoryInterface $catalog) { $this->catalog = $catalog; }

    public function handle(UpdateProductCommand $command): void
    {
        $product = $this->catalog->findProductById($command->productId);
        if ($product === null || $product->storeId() !== $command->storeId) {
            throw ProductNotFoundException::withId($command->productId);
        }
        $category = $this->catalog->findCategoryById($command->categoryId);
        if ($category === null || $category->storeId() !== $command->storeId) {
            throw CategoryNotFoundException::withId($command->categoryId);
        }
        $product->rename($command->name);
        $product->setBrand($command->brand);
        $product->setPrice($command->price);
        $this->catalog->saveProduct($product);
    }
}
