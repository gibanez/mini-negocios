<?php

declare(strict_types=1);

use Gaby\Apps\Api\Auth\Infrastructure\Http\Middleware\JwtAuthMiddleware;
use Gaby\Apps\Api\CLI\Infrastructure\Http\ClientController;
use Slim\Routing\RouteCollectorProxy;

$group->group('/clients', function (RouteCollectorProxy $cli): void {
    $cli->get('',         [ClientController::class, 'list']);
    $cli->get('/{id}',    [ClientController::class, 'show']);
    $cli->post('',        [ClientController::class, 'create']);
    $cli->put('/{id}',    [ClientController::class, 'update']);
    $cli->delete('/{id}', [ClientController::class, 'deactivate']);
})->add(JwtAuthMiddleware::class);
