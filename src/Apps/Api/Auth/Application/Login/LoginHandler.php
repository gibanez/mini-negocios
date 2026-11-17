<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Auth\Application\Login;

use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;
use Gaby\Apps\Api\Auth\Domain\Exception\InvalidCredentialsException;
use Gaby\Apps\Api\Auth\Domain\TokenGeneratorInterface;
use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;

final class LoginHandler
{
    /** @var AdminRepositoryInterface */
    private $admins;

    /** @var TokenGeneratorInterface */
    private $tokens;

    /** @var StoreRepositoryInterface */
    private $stores;

    public function __construct(
        AdminRepositoryInterface $admins,
        TokenGeneratorInterface $tokens,
        StoreRepositoryInterface $stores
    ) {
        $this->admins = $admins;
        $this->tokens = $tokens;
        $this->stores = $stores;
    }

    public function handle(LoginCommand $command): LoginResult
    {
        $admin = $this->admins->findByEmail($command->email);

        if ($admin === null || !$admin->isActive()) {
            throw InvalidCredentialsException::create();
        }

        if (!password_verify($command->password, $admin->passwordHash())) {
            throw InvalidCredentialsException::create();
        }

        $storeId = $admin->storeId();
        $store   = $storeId !== null ? $this->stores->findById($storeId) : null;

        if ($store !== null && !$store->isActive()) {
            throw InvalidCredentialsException::suspended();
        }

        $token = $this->tokens->generate($admin->id(), $admin->email(), (string) $storeId, $admin->role());

        return new LoginResult($token, [
            'id'                  => $admin->id(),
            'name'                => $admin->name(),
            'email'               => $admin->email(),
            'role'                => $admin->role(),
            'storeId'             => $storeId,
            'onboardingCompleted' => $store ? $store->onboardingCompleted() : false,
            'theme'               => $store ? $store->theme() : 'verde',
        ]);
    }
}
