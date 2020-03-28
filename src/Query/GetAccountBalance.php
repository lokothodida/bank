<?php

namespace lokothodida\Bank\Query;

interface GetAccountBalance
{
    public function __invoke(string $accountId): AccountBalance;
}
