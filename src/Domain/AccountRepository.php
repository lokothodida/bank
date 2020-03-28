<?php

namespace lokothodida\Bank\Domain;

interface AccountRepository
{
    public function newAccountId(): string;
    public function get(string $accountId): Account;
    public function set(string $accountId, Account $account): void;
}
