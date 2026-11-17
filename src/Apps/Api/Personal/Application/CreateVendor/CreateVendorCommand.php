<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Personal\Application\CreateVendor;

final class CreateVendorCommand
{
    /** @var string */
    public $storeId;

    /** @var string */
    public $name;

    /** @var string */
    public $email;

    /** @var string */
    public $password;

    public function __construct(string $storeId, string $name, string $email, string $password)
    {
        $this->storeId  = $storeId;
        $this->name     = $name;
        $this->email    = $email;
        $this->password = $password;
    }
}
