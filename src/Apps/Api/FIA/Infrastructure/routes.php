<?php
declare(strict_types=1);
use Gaby\Apps\Api\Auth\Infrastructure\Http\Middleware\JwtAuthMiddleware;
use Gaby\Apps\Api\FIA\Infrastructure\Http\FiadoController;
use Slim\Routing\RouteCollectorProxy;

// Ruta pública: sin JWT
$group->get('/fiado/public/{token}', [FiadoController::class, 'publicSummary']);

// Rutas protegidas
$group->group('/fiado', function (RouteCollectorProxy $fia): void {
    $fia->get('', [FiadoController::class, 'list']);
    $fia->get('/{clientId}', [FiadoController::class, 'getAccount']);
    $fia->post('/{clientId}/debts', [FiadoController::class, 'addDebt']);
    $fia->post('/{clientId}/payments', [FiadoController::class, 'addPayment']);
    $fia->delete('/{clientId}/payments/{movementId}', [FiadoController::class, 'cancelPayment']);
    $fia->post('/{clientId}/renew-link', [FiadoController::class, 'renewToken']);
})->add(JwtAuthMiddleware::class);
