<?php

use Phinx\Migration\AbstractMigration;

class CreateBankTransactionsTable extends AbstractMigration
{
    public function change()
    {
        $this
            ->table('bank_transactions')
            ->addColumn('account_number', 'string')
            ->addColumn('version', 'integer')
            ->addColumn('type', 'string')
            ->addColumn('recorded_at', 'datetime')
            ->addColumn('payload', 'json')
            ->create();
    }
}
