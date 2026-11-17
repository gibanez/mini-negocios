<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ACC\Application\Register;

final class RegisterCommand
{
    /** @var string */
    public $storeName;

    /** @var string */
    public $ownerName;

    /** @var string */
    public $email;

    /** @var string */
    public $password;

    public function __construct(string $storeName, string $ownerName, string $email, string $password)
    {
        $this->storeName = $storeName;
        $this->ownerName = $ownerName;
        $this->email     = $email;
        $this->password  = $password;
    }
}
