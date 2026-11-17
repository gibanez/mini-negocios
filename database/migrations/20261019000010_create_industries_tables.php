<?php

use Phinx\Migration\AbstractMigration;

final class CreateIndustriesTables extends AbstractMigration
{
    public function change(): void
    {
        $this->table('industries', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36])
            ->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('description', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->create();

        $this->table('industry_categories', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36])
            ->addColumn('industry_id', 'string', ['limit' => 36])
            ->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('position', 'integer', ['default' => 0])
            ->addIndex(['industry_id'])
            ->create();

        $this->table('industry_products', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36])
            ->addColumn('industry_category_id', 'string', ['limit' => 36])
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('unit', 'string', ['limit' => 30])
            ->addColumn('position', 'integer', ['default' => 0])
            ->addIndex(['industry_category_id'])
            ->create();
    }
}
