<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\UpdateCategory;
final class UpdateCategoryCommand
{
    /** @var string */ public $storeId;
    /** @var string */ public $categoryId;
    /** @var string */ public $name;
    public function __construct(string $storeId, string $categoryId, string $name)
    { $this->storeId=$storeId; $this->categoryId=$categoryId; $this->name=$name; }
}
