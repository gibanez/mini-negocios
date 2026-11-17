<?php

declare(strict_types=1);

use Gaby\Apps\Api\Auth\Infrastructure\Http\Middleware\JwtAuthMiddleware;
use Gaby\Apps\Api\PLA\Infrastructure\Http\Middleware\PlatformAdminMiddleware;
use Gaby\Apps\Api\PLA\Infrastructure\Http\PlatformController;
use Slim\Routing\RouteCollectorProxy;

$group->group('/platform', function (RouteCollectorProxy $pla): void {
    $pla->get('/stores',                    [PlatformController::class, 'listStores']);
    $pla->get('/stores/{id}',               [PlatformController::class, 'getStore']);
    $pla->put('/stores/{id}',               [PlatformController::class, 'updateStore']);
    $pla->post('/stores/{id}/suspend',      [PlatformController::class, 'suspendStore']);
    $pla->post('/stores/{id}/activate',     [PlatformController::class, 'activateStore']);
    $pla->get('/stores/{id}/admins',        [PlatformController::class, 'listStoreAdmins']);
    $pla->post('/stores/{id}/admins',       [PlatformController::class, 'createStoreAdmin']);
    $pla->delete('/admins/{adminId}',       [PlatformController::class, 'deactivateAdmin']);
    $pla->get('/stats',                     [PlatformController::class, 'stats']);
})->add(PlatformAdminMiddleware::class)->add(JwtAuthMiddleware::class);
