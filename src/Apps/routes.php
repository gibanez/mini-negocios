<?php

declare(strict_types=1);

use Slim\App;

return static function (App $app): void {
    require __DIR__ . '/Site/Infrastructure/routes.php';
    require __DIR__ . '/Api/routes.php';

    // Admin SPA — must come before the main frontend catch-all
    $adminHandler = function ($request, $response) {
        $index = __DIR__ . '/../../public/admin/index.html';
        if (!file_exists($index)) {
            $response->getBody()->write('Admin not built. Run: cd admin && npm run build');
            return $response->withStatus(404);
        }
        $response->getBody()->write(file_get_contents($index));
        return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
    };
    $app->get('/admin', $adminHandler);
    $app->get('/admin/{path:.+}', $adminHandler);

    // Catch-all: serve Vue SPA for non-API routes (must be last)
    $spaHandler = function ($request, $response) {
        $index = __DIR__ . '/../../public/index.html';
        if (!file_exists($index)) {
            $response->getBody()->write('Frontend not built. Run: cd frontend && npm run build');
            return $response->withStatus(404);
        }
        $response->getBody()->write(file_get_contents($index));
        return $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
    };
    $app->get('/', $spaHandler);
    $app->get('/{path:(?!api(?:/|$))[^.]+[/\w-]*}', $spaHandler);
};
