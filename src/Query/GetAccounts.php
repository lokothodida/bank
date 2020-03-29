<?php


namespace lokothodida\Bank\Query;

use lokothodida\Bank\Query\Exception\CustomerNotFound;
use lokothodida\Bank\Query\Model\Account;

interface GetAccounts
{
    /**
     * @throws CustomerNotFound
     * @param string $customerId
     * @return Account[]
     */
    public function __invoke(string $customerId): array;
}
