<?php

use Phinx\Migration\AbstractMigration;

final class CreateClientsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('clients', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36])
            ->addColumn('store_id', 'string', ['limit' => 36])
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('nickname', 'string', ['limit' => 100, 'null' => true, 'default' => null])
            ->addColumn('phone', 'string', ['limit' => 50, 'null' => true, 'default' => null])
            ->addColumn('address', 'string', ['limit' => 255, 'null' => true, 'default' => null])
            ->addColumn('notes', 'text', ['null' => true, 'default' => null])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('created_at', 'datetime')
            ->addIndex(['store_id'])
            ->create();
    }
}
