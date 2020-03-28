<?php

namespace lokothodida\Bank\Command;

use lokothodida\Bank\Domain\AccountRepository;
use lokothodida\Bank\Domain\Clock;

final class UnfreezeAccount
{
    private AccountRepository $accounts;
    private Clock $clock;

    public function __construct(AccountRepository $accounts, Clock $clock)
    {
        $this->accounts = $accounts;
        $this->clock = $clock;
    }

    public function __invoke(string $accountId): void
    {
        $this->accounts->set(
            $accountId,
            $this->accounts->get($accountId)->unfreeze($this->clock->now())
        );
    }
}
