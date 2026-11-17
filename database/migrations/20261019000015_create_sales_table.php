<?php

use Phinx\Migration\AbstractMigration;

final class CreateSalesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('sales', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36])
            ->addColumn('store_id', 'string', ['limit' => 36])
            ->addColumn('client_id', 'string', ['limit' => 36, 'null' => true, 'default' => null])
            ->addColumn('total', 'biginteger')
            ->addColumn('payment_method', 'string', ['limit' => 30])
            ->addColumn('responsible_id', 'string', ['limit' => 36])
            ->addColumn('responsible_name', 'string', ['limit' => 150])
            ->addColumn('notes', 'text', ['null' => true, 'default' => null])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'completed'])
            ->addColumn('cancelled_at', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('cancel_reason', 'text', ['null' => true, 'default' => null])
            ->addColumn('cancelled_by', 'string', ['limit' => 150, 'null' => true, 'default' => null])
            ->addColumn('created_at', 'datetime')
            ->addIndex(['store_id'])
            ->addIndex(['client_id'])
            ->create();
    }
}
