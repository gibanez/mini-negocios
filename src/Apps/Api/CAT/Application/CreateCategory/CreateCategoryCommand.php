<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\CreateCategory;
final class CreateCategoryCommand
{
    /** @var string */ public $storeId;
    /** @var string */ public $name;
    public function __construct(string $storeId, string $name) { $this->storeId=$storeId; $this->name=$name; }
}
