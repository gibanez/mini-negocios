<?php
declare(strict_types=1);
namespace Gaby\Apps\Api\CAT\Application\CreateProduct;
final class CreateProductCommand
{
    /** @var string */      public $storeId;
    /** @var string */      public $categoryId;
    /** @var string */      public $name;
    /** @var string|null */ public $brand;
    /** @var string */      public $unit;
    /** @var int|null */    public $price;
    /** @var int */         public $initialStock;
    public function __construct(string $storeId, string $categoryId, string $name,
        ?string $brand, string $unit, ?int $price, int $initialStock)
    {
        $this->storeId=$storeId; $this->categoryId=$categoryId; $this->name=$name;
        $this->brand=$brand; $this->unit=$unit; $this->price=$price; $this->initialStock=$initialStock;
    }
}
