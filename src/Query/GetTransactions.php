<?php

namespace lokothodida\Bank\Query;

use lokothodida\Bank\Query\Exception\AccountNotFound;
use lokothodida\Bank\Query\Model\Transaction;

interface GetTransactions
{
    /**
     * @throws AccountNotFound
     * @return Transaction[]
     */
    public function __invoke(string $accountId): array;
}
