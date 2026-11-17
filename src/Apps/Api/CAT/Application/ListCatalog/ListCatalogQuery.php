<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\CAT\Application\ListCatalog;

final class ListCatalogQuery
{
    /** @var string */
    public $storeId;

    public function __construct(string $storeId)
    {
        $this->storeId = $storeId;
    }
}
