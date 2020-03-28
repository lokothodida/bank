<?php

namespace lokothodida\Bank\Domain;

use lokothodida\Bank\Exception\AccountNotFound;

interface AccountRepository
{
    public function newAccountId(): string;

    /**
     * @@throws AccountNotFound
     */
    public function get(string $accountId): Account;

    public function set(string $accountId, Account $account): void;
}
