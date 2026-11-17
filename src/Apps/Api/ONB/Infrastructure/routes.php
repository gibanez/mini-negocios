<?php

declare(strict_types=1);

use Gaby\Apps\Api\ONB\Infrastructure\Http\OnboardingController;
use Gaby\Apps\Api\Auth\Infrastructure\Http\Middleware\JwtAuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

$group->group('', function (RouteCollectorProxy $auth) {
    $auth->get('/onboarding',                   [OnboardingController::class, 'status']);
    $auth->put('/onboarding/name',              [OnboardingController::class, 'setName']);
    $auth->put('/onboarding/theme',             [OnboardingController::class, 'setTheme']);
    $auth->post('/onboarding/industry',         [OnboardingController::class, 'chooseIndustry']);
    $auth->post('/onboarding/complete',         [OnboardingController::class, 'complete']);
})->add(JwtAuthMiddleware::class);
