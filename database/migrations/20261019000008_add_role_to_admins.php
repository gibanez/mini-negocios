<?php

use Phinx\Migration\AbstractMigration;

final class AddRoleToAdmins extends AbstractMigration
{
    public function up(): void
    {
        $this->table('admins')
            ->addColumn('role', 'string', ['limit' => 20, 'default' => 'owner', 'after' => 'status'])
            ->save();
    }

    public function down(): void
    {
        // SQLite 3.31 no soporta DROP COLUMN; se deja la columna sin usar
    }
}
