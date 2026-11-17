<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\CreateCategory;

use Gaby\Apps\Api\CAT\Domain\Exception\CategoryNameAlreadyUsedException;
use Gaby\Apps\Api\CAT\Domain\StoreCategory;
use Gaby\Apps\Api\CAT\Domain\StoreCatalogRepositoryInterface;

final class CreateCategoryHandler
{
    /** @var StoreCatalogRepositoryInterface */ private $catalog;
    public function __construct(StoreCatalogRepositoryInterface $catalog) { $this->catalog = $catalog; }

    public function handle(CreateCategoryCommand $command): StoreCategory
    {
        $existing = $this->catalog->findCategoryByNameAndStoreId($command->name, $command->storeId);
        if ($existing !== null) {
            throw CategoryNameAlreadyUsedException::withName($command->name);
        }
        $category = StoreCategory::create($command->storeId, $command->name);
        $this->catalog->saveCategory($category);
        return $category;
    }
}
