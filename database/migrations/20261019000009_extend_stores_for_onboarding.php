<?php

use Phinx\Migration\AbstractMigration;

final class ExtendStoresForOnboarding extends AbstractMigration
{
    public function up(): void
    {
        $this->table('stores')
            ->addColumn('onboarding_completed', 'boolean', ['default' => false])
            ->addColumn('theme', 'string', ['limit' => 50, 'default' => 'verde'])
            ->addColumn('logo_path', 'string', ['limit' => 500, 'null' => true, 'default' => null])
            ->save();
    }

    public function down(): void
    {
        // SQLite 3.31 — se dejan columnas sin usar
    }
}
