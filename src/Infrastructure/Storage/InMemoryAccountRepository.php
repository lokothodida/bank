<?php

namespace lokothodida\Bank\Infrastructure\Storage;

use lokothodida\Bank\Domain\Account;
use lokothodida\Bank\Domain\AccountRepository;
use lokothodida\Bank\Domain\Exception\AccountNotFound;

final class InMemoryAccountRepository implements AccountRepository
{
    /** @var Account[] */
    private array $accounts = [];

    public function newAccountId(): string
    {
        return (string) count($this->accounts);
    }

    public function get(string $accountId): Account
    {
        if (!isset($this->accounts[$accountId])) {
            throw new AccountNotFound($accountId);
        }

        return $this->accounts[$accountId];
    }

    public function set(string $accountId, Account $account): void
    {
        $this->accounts[$accountId] = $account;
    }
}
