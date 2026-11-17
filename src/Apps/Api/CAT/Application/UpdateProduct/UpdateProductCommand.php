<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\UpdateProduct;
final class UpdateProductCommand
{
    /** @var string */      public $storeId;
    /** @var string */      public $productId;
    /** @var string */      public $name;
    /** @var string|null */ public $brand;
    /** @var string */      public $categoryId;
    /** @var string */      public $unit;
    /** @var int|null */    public $price;
    public function __construct(string $storeId, string $productId, string $name,
        ?string $brand, string $categoryId, string $unit, ?int $price)
    {
        $this->storeId=$storeId; $this->productId=$productId; $this->name=$name;
        $this->brand=$brand; $this->categoryId=$categoryId; $this->unit=$unit; $this->price=$price;
    }
}
