<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Personal\Application\ListPersonal;

final class ListPersonalQuery
{
    /** @var string */
    public $storeId;

    public function __construct(string $storeId)
    {
        $this->storeId = $storeId;
    }
}
