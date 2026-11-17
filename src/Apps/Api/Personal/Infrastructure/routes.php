<?php

declare(strict_types=1);

use Gaby\Apps\Api\Auth\Infrastructure\Http\Middleware\JwtAuthMiddleware;
use Gaby\Apps\Api\Personal\Infrastructure\Http\PersonalController;
use Slim\Routing\RouteCollectorProxy;

$group->group('/personal', function (RouteCollectorProxy $personal): void {
    $personal->get('',         [PersonalController::class, 'list']);
    $personal->post('',        [PersonalController::class, 'create']);
    $personal->delete('/{id}', [PersonalController::class, 'deactivate']);
})->add(JwtAuthMiddleware::class);
