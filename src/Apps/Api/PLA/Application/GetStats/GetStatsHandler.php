<?php

declare(strict_types=1);

namespace Gaby\Apps\Api\PLA\Application\GetStats;

use PDO;

final class GetStatsHandler
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @return array<string,mixed> */
    public function handle(GetStatsQuery $q): array
    {
        return [
            'totalStores'     => (int) $this->pdo->query("SELECT COUNT(*) FROM stores")->fetchColumn(),
            'activeStores'    => (int) $this->pdo->query("SELECT COUNT(*) FROM stores WHERE status='active'")->fetchColumn(),
            'suspendedStores' => (int) $this->pdo->query("SELECT COUNT(*) FROM stores WHERE status='suspended'")->fetchColumn(),
            'totalAdmins'     => (int) $this->pdo->query("SELECT COUNT(*) FROM admins WHERE status='active'")->fetchColumn(),
            'totalSales'      => (int) $this->pdo->query("SELECT COUNT(*) FROM sales WHERE status='completed'")->fetchColumn(),
            'totalRevenue'    => (int) $this->pdo->query("SELECT COALESCE(SUM(total),0) FROM sales WHERE status='completed'")->fetchColumn(),
        ];
    }
}
