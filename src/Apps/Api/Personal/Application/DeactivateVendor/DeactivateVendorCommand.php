<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Personal\Application\DeactivateVendor;

final class DeactivateVendorCommand
{
    /** @var string */
    public $storeId;

    /** @var string */
    public $vendorId;

    public function __construct(string $storeId, string $vendorId)
    {
        $this->storeId  = $storeId;
        $this->vendorId = $vendorId;
    }
}
