<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\DeactivateCategory;
final class DeactivateCategoryCommand
{
    /** @var string */ public $storeId;
    /** @var string */ public $categoryId;
    public function __construct(string $storeId, string $categoryId) { $this->storeId=$storeId; $this->categoryId=$categoryId; }
}
