<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ACC\Application\Register;

use Gaby\Apps\Api\ACC\Domain\Exception\EmailAlreadyInUseException;
use Gaby\Apps\Api\Admin\Domain\Admin;
use Gaby\Apps\Api\Admin\Domain\AdminRepositoryInterface;
use Gaby\Apps\Api\Auth\Domain\TokenGeneratorInterface;
use Gaby\Apps\Api\Store\Domain\Store;
use Gaby\Apps\Api\Store\Domain\StoreRepositoryInterface;

final class RegisterHandler
{
    /** @var StoreRepositoryInterface */
    private $stores;

    /** @var AdminRepositoryInterface */
    private $admins;

    /** @var TokenGeneratorInterface */
    private $tokens;

    public function __construct(
        StoreRepositoryInterface $stores,
        AdminRepositoryInterface $admins,
        TokenGeneratorInterface $tokens
    ) {
        $this->stores = $stores;
        $this->admins = $admins;
        $this->tokens = $tokens;
    }

    public function handle(RegisterCommand $command): RegisterResult
    {
        $existing = $this->admins->findByEmail($command->email);

        if ($existing !== null) {
            throw EmailAlreadyInUseException::withEmail($command->email);
        }

        $store = Store::create($command->storeName);
        $this->stores->save($store);

        $owner = Admin::createOwner($command->ownerName, $command->email, $command->password, $store->id());
        $this->admins->save($owner);

        $token = $this->tokens->generate($owner->id(), $owner->email(), $store->id(), $owner->role());

        return new RegisterResult($token, [
            'id'                  => $owner->id(),
            'name'                => $owner->name(),
            'email'               => $owner->email(),
            'role'                => $owner->role(),
            'storeId'             => $store->id(),
            'onboardingCompleted' => false,
            'theme'               => 'verde',
        ]);
    }
}
