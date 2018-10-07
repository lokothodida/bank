<?php

namespace lokothodida\Bank;

final class Bank
{
    private $vault;
    private $clock;

    public function __construct(Vault $vault, Clock $clock)
    {
        $this->vault = $vault;
        $this->clock = $clock;
    }

    public function openAccount(): string
    {
        $accountNumber = $this->vault->generateAccountNumber();
        $this->vault->recordThatAccountWasOpened(Account::open($accountNumber, $this->clock->now()));

        return (string)$accountNumber;
    }

    public function depositIntoAccount(string $accountNumber, int $amount): void
    {
        $account = $this->vault->findAccountByAccountNumber(new AccountNumber($accountNumber));
        $this->vault->recordThatFundsWereDeposited(
            $account->deposit(Money::Gbp($amount), $this->clock->now())
        );
    }

    public function withdrawFromAccount(string $accountNumber, int $amount): void
    {
        $account = $this->vault->findAccountByAccountNumber(new AccountNumber($accountNumber));
        $this->vault->recordThatFundsWereWithdrawn(
            $account->withdraw(Money::Gbp($amount), $this->clock->now())
        );
    }

    public function transferToAccount(string $fromAccountNumber, string $toAccountNumber, int $amount): void
    {
        $fromAccount = $this->vault->findAccountByAccountNumber(new AccountNumber($fromAccountNumber));
        $toAccount = $this->vault->findAccountByAccountNumber(new AccountNumber($toAccountNumber));
        $this->vault->recordThatFundsWereTransferred(
            $fromAccount->transfer(Money::Gbp($amount), $toAccount, $this->clock->now())
        );
    }

    public function freezeAccount(string $accountNumber): void
    {
        $account = $this->vault->findAccountByAccountNumber(new AccountNumber($accountNumber));
        $this->vault->recordThatAccountWasFrozen(
            $account->freeze($this->clock->now())
        );
    }
}
