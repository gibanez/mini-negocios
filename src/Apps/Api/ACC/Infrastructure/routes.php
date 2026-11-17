<?php

declare(strict_types=1);

use Gaby\Apps\Api\ACC\Infrastructure\Http\RegisterController;

$group->post('/auth/register', [RegisterController::class, 'register']);
