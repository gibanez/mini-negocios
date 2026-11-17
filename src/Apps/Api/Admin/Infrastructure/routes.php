<?php

declare(strict_types=1);

use Gaby\Apps\Api\Admin\Infrastructure\Http\UserController;
use Gaby\Apps\Api\Auth\Infrastructure\Http\Middleware\JwtAuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

$group->group('', function (RouteCollectorProxy $auth) {
    $auth->get('/users',        [UserController::class, 'list']);
    $auth->post('/users',       [UserController::class, 'create']);
    $auth->put('/users/{id}',   [UserController::class, 'update']);
    $auth->delete('/users/{id}',[UserController::class, 'delete']);
    $auth->delete('/me',        [UserController::class, 'deleteMe']);
})->add(JwtAuthMiddleware::class);
