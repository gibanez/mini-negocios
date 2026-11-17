<?php

use Phinx\Migration\AbstractMigration;

final class CreateStoresTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('stores', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36])
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('industry', 'string', ['limit' => 100, 'null' => true, 'default' => null])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('created_at', 'datetime')
            ->create();
    }
}
