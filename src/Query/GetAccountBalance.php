<?php

namespace lokothodida\Bank\Query;

use lokothodida\Bank\Query\Exception\AccountNotFound;
use lokothodida\Bank\Query\Model\AccountBalance;

interface GetAccountBalance
{
    /**
     * @throws AccountNotFound
     */
    public function __invoke(string $accountId): AccountBalance;
}
