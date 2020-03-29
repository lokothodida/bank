<?php

namespace lokothodida\Bank\Command;

use lokothodida\Bank\Domain\Account;
use lokothodida\Bank\Domain\AccountRepository;
use lokothodida\Bank\Domain\Clock;

final class OpenAccount
{
    private AccountRepository $accounts;
    private Clock $clock;

    public function __construct(AccountRepository $accounts, Clock $clock)
    {
        $this->accounts = $accounts;
        $this->clock = $clock;
    }

    public function __invoke(string $customerId): string
    {
        $accountId = $this->accounts->newAccountId();

        $this->accounts->set($accountId, Account::open($accountId, $customerId, $this->clock->now()));

        return $accountId;
    }
}
