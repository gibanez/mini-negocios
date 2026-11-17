<?php

use Phinx\Migration\AbstractMigration;

final class CreateSaleItemsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('sale_items', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36])
            ->addColumn('sale_id', 'string', ['limit' => 36])
            ->addColumn('store_product_id', 'string', ['limit' => 36, 'null' => true, 'default' => null])
            ->addColumn('product_name', 'string', ['limit' => 200])
            ->addColumn('quantity', 'biginteger')
            ->addColumn('unit_price', 'biginteger')
            ->addColumn('subtotal', 'biginteger')
            ->addIndex(['sale_id'])
            ->create();
    }
}
