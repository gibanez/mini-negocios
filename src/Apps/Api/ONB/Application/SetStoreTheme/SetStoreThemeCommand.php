<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ONB\Application\SetStoreTheme;

final class SetStoreThemeCommand
{
    /** @var string */
    public $storeId;
    /** @var string */
    public $theme;

    public function __construct(string $storeId, string $theme)
    {
        $this->storeId = $storeId;
        $this->theme   = $theme;
    }
}
