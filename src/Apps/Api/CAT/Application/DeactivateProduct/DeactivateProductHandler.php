<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\DeactivateProduct;

use Gaby\Apps\Api\CAT\Domain\Exception\ProductNotFoundException;
use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;

final class DeactivateProductHandler
{
    /** @var StoreCatalogRepositoryInterface */ private $catalog;
    public function __construct(StoreCatalogRepositoryInterface $catalog) { $this->catalog = $catalog; }

    public function handle(DeactivateProductCommand $command): void
    {
        $product = $this->catalog->findProductById($command->productId);
        if ($product === null || $product->storeId() !== $command->storeId) {
            throw ProductNotFoundException::withId($command->productId);
        }
        $product->deactivate();
        $this->catalog->saveProduct($product);
    }
}
