<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\Industry\Infrastructure\Persistence;

use Gaby\Apps\Api\Industry\Domain\Industry;
use Gaby\Apps\Api\Industry\Domain\IndustryCategory;
use Gaby\Apps\Api\Industry\Domain\IndustryProduct;
use Gaby\Apps\Api\Industry\Domain\IndustryRepositoryInterface;
use PDO;

final class SqliteIndustryRepository implements IndustryRepositoryInterface
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAllActive(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM industries WHERE status = 'active' ORDER BY rowid ASC");
        return array_map([$this, 'hydrateIndustry'], $stmt->fetchAll());
    }

    public function findById(string $id): ?Industry
    {
        $stmt = $this->pdo->prepare('SELECT * FROM industries WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $this->hydrateIndustry($row);
    }

    public function findCategoriesByIndustryId(string $industryId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM industry_categories WHERE industry_id = :id ORDER BY position ASC');
        $stmt->execute(['id' => $industryId]);
        return array_map([$this, 'hydrateCategory'], $stmt->fetchAll());
    }

    public function findProductsByCategoryId(string $categoryId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM industry_products WHERE industry_category_id = :id ORDER BY position ASC');
        $stmt->execute(['id' => $categoryId]);
        return array_map([$this, 'hydrateProduct'], $stmt->fetchAll());
    }

    /**
     * @param array<string,mixed> $row
     */
    private function hydrateIndustry(array $row): Industry
    {
        return Industry::fromPersistence(
            (string) $row['id'],
            (string) $row['name'],
            isset($row['description']) ? (string) $row['description'] : null,
            (string) $row['status']
        );
    }

    /**
     * @param array<string,mixed> $row
     */
    private function hydrateCategory(array $row): IndustryCategory
    {
        return IndustryCategory::fromPersistence(
            (string) $row['id'],
            (string) $row['industry_id'],
            (string) $row['name'],
            (int) $row['position']
        );
    }

    /**
     * @param array<string,mixed> $row
     */
    private function hydrateProduct(array $row): IndustryProduct
    {
        return IndustryProduct::fromPersistence(
            (string) $row['id'],
            (string) $row['industry_category_id'],
            (string) $row['name'],
            (string) $row['unit'],
            (int) $row['position']
        );
    }
}
