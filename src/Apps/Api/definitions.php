<?php

declare(strict_types=1);

return array_merge(
    require __DIR__ . '/Admin/Infrastructure/definitions.php',
    require __DIR__ . '/Auth/Infrastructure/definitions.php',
    require __DIR__ . '/Store/Infrastructure/definitions.php',
    require __DIR__ . '/ACC/Infrastructure/definitions.php',
    require __DIR__ . '/Personal/Infrastructure/definitions.php',
    require __DIR__ . '/Industry/Infrastructure/definitions.php',
    require __DIR__ . '/ONB/Infrastructure/definitions.php',
    require __DIR__ . '/CAT/Infrastructure/definitions.php',
    require __DIR__ . '/CLI/Infrastructure/definitions.php',
    require __DIR__ . '/VEN/Infrastructure/definitions.php',
    require __DIR__ . '/FIA/Infrastructure/definitions.php',
    require __DIR__ . '/GAS/Infrastructure/definitions.php',
    require __DIR__ . '/PLA/Infrastructure/definitions.php'
);
