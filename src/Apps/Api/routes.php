<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

$app->group('/api', function (RouteCollectorProxy $group): void {
    $group->get('/ping', function (Request $request, Response $response): Response {
        $payload = ['status' => 'ok', 'time' => date('c')];
        $response->getBody()->write((string) json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    });

    require __DIR__ . '/Auth/Infrastructure/routes.php';
    require __DIR__ . '/Admin/Infrastructure/routes.php';
    require __DIR__ . '/ACC/Infrastructure/routes.php';
    require __DIR__ . '/Personal/Infrastructure/routes.php';
    require __DIR__ . '/Industry/Infrastructure/routes.php';
    require __DIR__ . '/ONB/Infrastructure/routes.php';
    require __DIR__ . '/CAT/Infrastructure/routes.php';
    require __DIR__ . '/CLI/Infrastructure/routes.php';
    require __DIR__ . '/VEN/Infrastructure/routes.php';
    require __DIR__ . '/FIA/Infrastructure/routes.php';
    require __DIR__ . '/PLA/Infrastructure/routes.php';
});
