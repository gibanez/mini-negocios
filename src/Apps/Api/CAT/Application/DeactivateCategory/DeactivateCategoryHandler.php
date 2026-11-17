<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\DeactivateCategory;

use Gaby\Apps\Api\CAT\Domain\Exception\CategoryNotFoundException;
use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;

final class DeactivateCategoryHandler
{
    /** @var StoreCatalogRepositoryInterface */ private $catalog;
    public function __construct(StoreCatalogRepositoryInterface $catalog) { $this->catalog = $catalog; }

    public function handle(DeactivateCategoryCommand $command): int
    {
        $category = $this->catalog->findCategoryById($command->categoryId);
        if ($category === null || $category->storeId() !== $command->storeId) {
            throw CategoryNotFoundException::withId($command->categoryId);
        }
        $count = $this->catalog->countActiveProductsByCategoryId($command->categoryId);
        $category->deactivate();
        $this->catalog->saveCategory($category);
        return $count;
    }
}
