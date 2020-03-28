<?php


namespace lokothodida\Bank\Query;

interface GetAccounts
{
    /**
     * @param string $customerId
     * @return Account[]
     */
    public function __invoke(string $customerId): array;
}
