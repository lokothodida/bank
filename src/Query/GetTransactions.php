<?php

namespace lokothodida\Bank\Query;

interface GetTransactions
{
    /**
     * @return Transaction[]
     */
    public function __invoke(string $accountId): array;
}
