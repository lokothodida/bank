<?php

namespace lokothodida\Bank\Query;

use lokothodida\Bank\Query\Exception\AccountNotFound;

interface GetAccountBalance
{
    /**
     * @throws AccountNotFound
     */
    public function __invoke(string $accountId): AccountBalance;
}
