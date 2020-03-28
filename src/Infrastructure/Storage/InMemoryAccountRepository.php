<?php

namespace lokothodida\Bank\Infrastructure\Storage;

use lokothodida\Bank\Domain\Account;
use lokothodida\Bank\Domain\AccountRepository;

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
        return $this->accounts[$accountId];
    }

    public function set(string $accountId, Account $account): void
    {
        $this->accounts[$accountId] = $account;
    }
}
