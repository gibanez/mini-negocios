<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Industry\Application\ListIndustries;

use Gaby\Apps\Api\Industry\Domain\IndustryRepositoryInterface;

final class ListIndustriesHandler
{
    /** @var IndustryRepositoryInterface */
    private $industries;

    public function __construct(IndustryRepositoryInterface $industries)
    {
        $this->industries = $industries;
    }

    /** @return array<int,array<string,mixed>> */
    public function handle(): array
    {
        $industries = $this->industries->findAllActive();

        return array_map(function ($ind): array {
            $categories   = $this->industries->findCategoriesByIndustryId($ind->id());
            $productCount = 0;

            foreach ($categories as $cat) {
                $productCount += count($this->industries->findProductsByCategoryId($cat->id()));
            }

            return array_merge($ind->toArray(), [
                'categoryCount' => count($categories),
                'productCount'  => $productCount,
            ]);
        }, $industries);
    }
}
