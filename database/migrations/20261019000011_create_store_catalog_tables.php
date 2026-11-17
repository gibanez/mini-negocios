<?php

use Phinx\Migration\AbstractMigration;

final class CreateStoreCatalogTables extends AbstractMigration
{
    public function change(): void
    {
        $this->table('store_categories', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36])
            ->addColumn('store_id', 'string', ['limit' => 36])
            ->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('position', 'integer', ['default' => 0])
            ->addIndex(['store_id'])
            ->create();

        $this->table('store_products', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36])
            ->addColumn('store_id', 'string', ['limit' => 36])
            ->addColumn('store_category_id', 'string', ['limit' => 36])
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('brand', 'string', ['limit' => 100, 'null' => true, 'default' => null])
            ->addColumn('unit', 'string', ['limit' => 30])
            ->addColumn('price', 'biginteger', ['null' => true, 'default' => null])
            ->addColumn('stock', 'biginteger', ['default' => 0])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addIndex(['store_id'])
            ->addIndex(['store_category_id'])
            ->create();
    }
}
