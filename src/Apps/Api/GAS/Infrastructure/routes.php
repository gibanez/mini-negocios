<?php

declare(strict_types=1);

use Gaby\Apps\Api\Auth\Infrastructure\Http\Middleware\JwtAuthMiddleware;
use Gaby\Apps\Api\GAS\Infrastructure\Http\ExpenseController;
use Slim\Routing\RouteCollectorProxy;

$group->group('/expenses', function (RouteCollectorProxy $gas): void {
    $gas->get('',       [ExpenseController::class, 'list']);
    $gas->get('/{id}',  [ExpenseController::class, 'show']);
    $gas->post('',      [ExpenseController::class, 'create']);
    $gas->put('/{id}',  [ExpenseController::class, 'update']);
    $gas->delete('/{id}', [ExpenseController::class, 'delete']);
})->add(JwtAuthMiddleware::class);
