<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Industry\Domain;

interface IndustryRepositoryInterface
{
    /** @return Industry[] */
    public function findAllActive(): array;

    public function findById(string $id): ?Industry;

    /** @return IndustryCategory[] */
    public function findCategoriesByIndustryId(string $industryId): array;

    /** @return IndustryProduct[] */
    public function findProductsByCategoryId(string $categoryId): array;
}
