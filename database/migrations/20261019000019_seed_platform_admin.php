<?php

use Phinx\Migration\AbstractMigration;

final class SeedPlatformAdmin extends AbstractMigration
{
    public function up(): void
    {
        $rows = $this->fetchAll("SELECT id FROM admins WHERE role='platform_admin' LIMIT 1");
        if (!empty($rows)) {
            return;
        }

        $id = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $hash = password_hash('Admin1234!', PASSWORD_BCRYPT);
        $now  = date('Y-m-d H:i:s');

        $this->execute(
            "INSERT INTO admins (id, name, email, password_hash, status, role, store_id)
             VALUES ('{$id}', 'Platform Admin', 'admin@despensa.app', '{$hash}', 'active', 'platform_admin', NULL)"
        );
    }

    public function down(): void
    {
        $this->execute("DELETE FROM admins WHERE role='platform_admin'");
    }
}
