<?php


namespace lokothodida\Bank\Command;


use lokothodida\Bank\Domain\AccountRepository;
use lokothodida\Bank\Domain\Clock;
use lokothodida\Bank\Domain\Money;

class WithdrawFromAccount
{
    private AccountRepository $accounts;
    private Clock $clock;

    public function __construct(AccountRepository $accounts, Clock $clock)
    {
        $this->accounts = $accounts;
        $this->clock = $clock;
    }

    public function __invoke(string $accountId, int $amount): void
    {
        $this->accounts->set(
            $accountId,
            $this->accounts->get($accountId)->withdraw(new Money($amount), $this->clock->now())
        );
    }
}
