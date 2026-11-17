<?php
use Phinx\Migration\AbstractMigration;
final class CreateCreditAccountsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('credit_accounts', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'string', ['limit' => 36])
            ->addColumn('store_id', 'string', ['limit' => 36])
            ->addColumn('client_id', 'string', ['limit' => 36])
            ->addColumn('public_token', 'string', ['limit' => 36])
            ->addColumn('status', 'string', ['limit' => 20, 'default' => 'active'])
            ->addColumn('created_at', 'datetime')
            ->addIndex(['store_id'])
            ->addIndex(['client_id'])
            ->addIndex(['public_token'], ['unique' => true])
            ->create();
    }
}
