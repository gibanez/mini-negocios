<?php
use Phinx\Migration\AbstractMigration;
final class CreateCreditMovementsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('credit_movements', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36])
            ->addColumn('credit_account_id', 'string', ['limit' => 36])
            ->addColumn('store_id', 'string', ['limit' => 36])
            ->addColumn('type', 'string', ['limit' => 20])
            ->addColumn('amount', 'biginteger')
            ->addColumn('sale_id', 'string', ['limit' => 36, 'null' => true, 'default' => null])
            ->addColumn('payment_method', 'string', ['limit' => 30, 'null' => true, 'default' => null])
            ->addColumn('notes', 'text', ['null' => true, 'default' => null])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('cancelled_at', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('cancel_reason', 'text', ['null' => true, 'default' => null])
            ->addColumn('cancelled_by', 'string', ['limit' => 150, 'null' => true, 'default' => null])
            ->addColumn('created_at', 'datetime')
            ->addIndex(['credit_account_id'])
            ->addIndex(['store_id'])
            ->create();
    }
}
