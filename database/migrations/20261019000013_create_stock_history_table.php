<?php
use Phinx\Migration\AbstractMigration;
final class CreateStockHistoryTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('stock_history', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36])
            ->addColumn('store_product_id', 'string', ['limit' => 36])
            ->addColumn('store_id', 'string', ['limit' => 36])
            ->addColumn('previous_stock', 'biginteger')
            ->addColumn('new_stock', 'biginteger')
            ->addColumn('delta', 'biginteger')
            ->addColumn('reason', 'string', ['limit' => 50])
            ->addColumn('note', 'text', ['null' => true, 'default' => null])
            ->addColumn('responsible_name', 'string', ['limit' => 150])
            ->addColumn('created_at', 'datetime')
            ->addIndex(['store_product_id'])
            ->addIndex(['store_id'])
            ->create();
    }
}
