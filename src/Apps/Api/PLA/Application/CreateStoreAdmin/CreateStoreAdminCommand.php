<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\CreateStoreAdmin;

final class CreateStoreAdminCommand
{
    /** @var string */
    public $storeId;

    /** @var string */
    public $name;

    /** @var string */
    public $email;

    /** @var string */
    public $password;

    /** @var string */
    public $role;

    public function __construct(string $storeId, string $name, string $email, string $password, string $role)
    {
        $this->storeId  = $storeId;
        $this->name     = $name;
        $this->email    = $email;
        $this->password = $password;
        $this->role     = $role;
    }
}
