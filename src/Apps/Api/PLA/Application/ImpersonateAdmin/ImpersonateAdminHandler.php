<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\ImpersonateAdmin;

use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;
use Gaby\Apps\Api\Auth\Domain\TokenGeneratorInterface;
use RuntimeException;
use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;

final class ImpersonateAdminHandler
{
    /** @var AdminRepositoryInterface */ private $admins;
    /** @var TokenGeneratorInterface */ private $tokens;
    /** @var StoreRepositoryInterface */ private $stores;

    public function __construct(
        AdminRepositoryInterface $admins,
        TokenGeneratorInterface $tokens,
        StoreRepositoryInterface $stores
    ) {
        $this->admins = $admins;
        $this->tokens = $tokens;
        $this->stores = $stores;
    }

    /** @return array<string,mixed> */
    public function handle(ImpersonateAdminCommand $command): array
    {
        $admin = $this->admins->findById($command->adminId);
        if ($admin === null) {
            throw new RuntimeException('Admin not found.');
        }

        $storeId = $admin->storeId();
        $store   = $storeId !== null ? $this->stores->findById($storeId) : null;

        $token = $this->tokens->generate(
            $admin->id(),
            $admin->email(),
            (string) $storeId,
            $admin->role()
        );

        return [
            'token' => $token,
            'user'  => [
                'id'                  => $admin->id(),
                'name'                => $admin->name(),
                'email'               => $admin->email(),
                'role'                => $admin->role(),
                'storeId'             => $storeId,
                'onboardingCompleted' => $store ? $store->onboardingCompleted() : false,
                'theme'               => $store ? $store->theme() : 'verde',
            ],
        ];
    }
}
