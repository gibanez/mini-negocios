<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateExpensesTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('expenses', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id',               'string',  ['limit' => 36])
            ->addColumn('store_id',         'string',  ['limit' => 36])
            ->addColumn('amount',           'biginteger')
            ->addColumn('category',         'string',  ['limit' => 50])
            ->addColumn('recipient',        'string',  ['limit' => 200])
            ->addColumn('description',      'text',    ['null' => true, 'default' => null])
            ->addColumn('payment_method',   'string',  ['limit' => 30])
            ->addColumn('responsible_id',   'string',  ['limit' => 36])
            ->addColumn('responsible_name', 'string',  ['limit' => 150])
            ->addColumn('date',             'date')
            ->addColumn('created_at',       'datetime')
            ->addIndex(['store_id'])
            ->addIndex(['date'])
            ->create();
    }
}
