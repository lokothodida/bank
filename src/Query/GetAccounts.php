<?php


namespace lokothodida\Bank\Query;

use lokothodida\Bank\Query\Exception\CustomerNotFound;

interface GetAccounts
{
    /**
     * @throws CustomerNotFound
     * @param string $customerId
     * @return Account[]
     */
    public function __invoke(string $customerId): array;
}
