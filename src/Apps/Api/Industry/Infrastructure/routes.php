<?php

declare(strict_types=1);

use Gaby\Apps\Api\Industry\Infrastructure\Http\IndustryController;

$group->get('/industries', [IndustryController::class, 'list']);
