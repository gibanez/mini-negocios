<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\UpdateCategory;

use Gaby\Apps\Api\CAT\Domain\Exception\CategoryNameAlreadyUsedException;
use Gaby\Apps\Api\CAT\Domain\Exception\CategoryNotFoundException;
use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;

final class UpdateCategoryHandler
{
    /** @var StoreCatalogRepositoryInterface */ private $catalog;
    public function __construct(StoreCatalogRepositoryInterface $catalog) { $this->catalog = $catalog; }

    public function handle(UpdateCategoryCommand $command): void
    {
        $category = $this->catalog->findCategoryById($command->categoryId);
        if ($category === null || $category->storeId() !== $command->storeId) {
            throw CategoryNotFoundException::withId($command->categoryId);
        }
        $duplicate = $this->catalog->findCategoryByNameAndStoreId($command->name, $command->storeId);
        if ($duplicate !== null && $duplicate->id() !== $command->categoryId) {
            throw CategoryNameAlreadyUsedException::withName($command->name);
        }
        $category->rename($command->name);
        $this->catalog->saveCategory($category);
    }
}
