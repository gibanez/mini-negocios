<?php
declare(strict_types=1);
use Gaby\Apps\Api\Auth\Infrastructure\Http\Middleware\JwtAuthMiddleware;
use Gaby\Apps\Api\CAT\Infrastructure\Http\CatalogController;
use Slim\Routing\RouteCollectorProxy;

$group->group('/catalog', function (RouteCollectorProxy $cat): void {
    $cat->get('',            [CatalogController::class, 'list']);
    $cat->get('/categories', [CatalogController::class, 'listCategories']);
    $cat->get('/products',   [CatalogController::class, 'listProducts']);
    $cat->get('/search',     [CatalogController::class, 'search']);

    $cat->post('/categories',        [CatalogController::class, 'createCategory']);
    $cat->put('/categories/{id}',    [CatalogController::class, 'updateCategory']);
    $cat->delete('/categories/{id}', [CatalogController::class, 'deactivateCategory']);

    $cat->post('/products',                   [CatalogController::class, 'createProduct']);
    $cat->put('/products/{id}',               [CatalogController::class, 'updateProduct']);
    $cat->delete('/products/{id}',            [CatalogController::class, 'deactivateProduct']);
    $cat->post('/products/{id}/stock',        [CatalogController::class, 'adjustStock']);
    $cat->get('/products/{id}/stock/history', [CatalogController::class, 'stockHistory']);
})->add(JwtAuthMiddleware::class);
