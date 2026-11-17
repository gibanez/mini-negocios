<?php

declare(strict_types=1);

use Gaby\Apps\Api\Auth\Infrastructure\Http\Middleware\JwtAuthMiddleware;
use Gaby\Apps\Api\VEN\Infrastructure\Http\SaleController;
use Slim\Routing\RouteCollectorProxy;

$group->group('/sales', function (RouteCollectorProxy $ven): void {
    $ven->get('',         [SaleController::class, 'list']);
    $ven->get('/{id}',    [SaleController::class, 'show']);
    $ven->post('',        [SaleController::class, 'create']);
    $ven->post('/{id}/cancel', [SaleController::class, 'cancel']);
})->add(JwtAuthMiddleware::class);
