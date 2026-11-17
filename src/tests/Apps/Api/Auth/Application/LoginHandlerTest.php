<?php

declare(strict_types=1);

use Gaby\Apps\Api\Admin\Domain\Admin;
use Gaby\Apps\Api\Auth\Application\Login\LoginCommand;
use Gaby\Apps\Api\Auth\Application\Login\LoginHandler;
use Gaby\Apps\Api\Auth\Domain\Exception\InvalidCredentialsException;
use Gaby\Apps\Api\Store\Domain\Store;
use Gaby\Tests\Apps\Api\Auth\Doubles\FakeTokenGenerator;
use Gaby\Tests\Apps\Api\Auth\Doubles\InMemoryAdminRepository;
use Gaby\Tests\Apps\Api\Auth\Doubles\InMemoryStoreRepository;

function makeLoginHandler(InMemoryAdminRepository $admins, InMemoryStoreRepository $stores): LoginHandler
{
    return new LoginHandler($admins, new FakeTokenGenerator(), $stores);
}

test('devuelve un token con credenciales validas', function (): void {
    $stores = new InMemoryStoreRepository();
    $store  = Store::create('Mi Comercio');
    $stores->save($store);

    $admins = new InMemoryAdminRepository();
    $admin  = Admin::createOwner('Admin', 'admin@example.com', 'secret', $store->id());
    $admins->save($admin);

    $result = makeLoginHandler($admins, $stores)->handle(new LoginCommand('admin@example.com', 'secret'));

    expect($result->token)->toBe('token-' . $admin->id());
    expect($result->user['email'])->toBe('admin@example.com');
    expect($result->user['role'])->toBe('owner');
});

test('rechaza credenciales invalidas', function (): void {
    $stores = new InMemoryStoreRepository();
    $store  = Store::create('Mi Comercio');
    $stores->save($store);

    $admins = new InMemoryAdminRepository();
    $admins->save(Admin::createOwner('Admin', 'admin@example.com', 'secret', $store->id()));

    expect(function () use ($admins, $stores): void {
        makeLoginHandler($admins, $stores)->handle(new LoginCommand('admin@example.com', 'wrong'));
    })->toThrow(InvalidCredentialsException::class);
});

test('rechaza un admin inactivo', function (): void {
    $stores = new InMemoryStoreRepository();
    $store  = Store::create('Mi Comercio');
    $stores->save($store);

    $admins = new InMemoryAdminRepository();
    $admin  = Admin::createOwner('Admin', 'admin@example.com', 'secret', $store->id());
    $admin->deactivate();
    $admins->save($admin);

    expect(function () use ($admins, $stores): void {
        makeLoginHandler($admins, $stores)->handle(new LoginCommand('admin@example.com', 'secret'));
    })->toThrow(InvalidCredentialsException::class);
});

test('rechaza login si el comercio esta suspendido', function (): void {
    $stores = new InMemoryStoreRepository();
    $store  = Store::create('Mi Comercio');
    $store->suspend();
    $stores->save($store);

    $admins = new InMemoryAdminRepository();
    $admins->save(Admin::createOwner('Admin', 'admin@example.com', 'secret', $store->id()));

    expect(function () use ($admins, $stores): void {
        makeLoginHandler($admins, $stores)->handle(new LoginCommand('admin@example.com', 'secret'));
    })->toThrow(InvalidCredentialsException::class);
});
