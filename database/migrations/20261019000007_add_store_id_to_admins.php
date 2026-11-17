<?php

use Phinx\Migration\AbstractMigration;

final class AddStoreIdToAdmins extends AbstractMigration
{
    public function up(): void
    {
        $this->table('admins')
            ->addColumn('store_id', 'string', ['limit' => 36, 'null' => true, 'default' => null])
            ->save();

        $storeId = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        $now = date('Y-m-d H:i:s');

        $this->execute("INSERT INTO stores (id, name, status, created_at) VALUES ('$storeId', 'Mi Comercio', 'active', '$now')");
        $this->execute("UPDATE admins SET store_id = '$storeId'");
    }

    public function down(): void
    {
        $this->execute("CREATE TABLE admins_backup AS SELECT id, name, email, password_hash, status FROM admins");
        $this->execute("DROP TABLE admins");
        $this->execute("ALTER TABLE admins_backup RENAME TO admins");
        $this->execute("DELETE FROM stores WHERE name = 'Mi Comercio'");
    }
}
