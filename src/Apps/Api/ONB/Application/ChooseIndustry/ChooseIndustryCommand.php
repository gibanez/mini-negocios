<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\ONB\Application\ChooseIndustry;

final class ChooseIndustryCommand
{
    /** @var string */
    public $storeId;
    /** @var string */
    public $industryId;

    public function __construct(string $storeId, string $industryId)
    {
        $this->storeId    = $storeId;
        $this->industryId = $industryId;
    }
}
