<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ACC\Application\Register;

final class RegisterResult
{
    /** @var string */
    public $token;

    /** @var array<string, string> */
    public $user;

    /**
     * @param array<string, string> $user
     */
    public function __construct(string $token, array $user)
    {
        $this->token = $token;
        $this->user  = $user;
    }
}
