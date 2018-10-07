<?php

namespace lokothodida\Bank\Vaults;

use lokothodida\Bank\Vault;
use lokothodida\Bank\Account;
use lokothodida\Bank\AccountNumber;
use lokothodida\Bank\Money;
use lokothodida\Bank\Version;
use lokothodida\Bank\Transaction;
use lokothodida\Bank\Transactions\{
    AccountOpened,
    FundsDepositedIntoAccount,
    FundsWithdrawnFromAccount,
    AccountFrozen,
    FundsTransferredBetweenAccounts
};
use DomainException;

final class InMemoryVault implements Vault
{
    private $accounts = [];
    private $lastAccountNumber = '10000000';

    public function generateAccountNumber(): AccountNumber
    {
        return new AccountNumber($this->lastAccountNumber++);
    }

    public function findAccountByAccountNumber(AccountNumber $accountNumber): Account
    {
        return new Account(
            $accountNumber,
            new Version(1),
            $this->accounts[(string)$accountNumber]['balance'],
            $this->accounts[(string)$accountNumber]['isFrozen']
        );
    }

    public function recordThatAccountWasOpened(AccountOpened $occurred): void
    {
        $this->accounts[(string)$occurred->accountNumber()] = [
            'balance' => Money::Gbp(0),
            'isFrozen' => false
        ];
    }

    public function recordThatFundsWereDeposited(FundsDepositedIntoAccount $occurred): void
    {
        $accountNumber = (string)$occurred->accountNumber();
        $this->accounts[$accountNumber]['balance'] =
            $this->accounts[$accountNumber]['balance']->add($occurred->funds());
    }

    public function recordThatFundsWereWithdrawn(FundsWithdrawnFromAccount $occurred): void
    {
        $accountNumber = (string)$occurred->accountNumber();
        $this->accounts[$accountNumber]['balance'] =
            $this->accounts[$accountNumber]['balance']->subtract($occurred->funds());
    }

    public function recordThatFundsWereTransferred(FundsTransferredBetweenAccounts $occurred): void
    {
        $fromAccount = (string)$occurred->fromAccountNumber();
        $toAccount = (string)$occurred->toAccountNumber();

        $this->accounts[$fromAccount]['balance'] =
            $this->accounts[$fromAccount]['balance']->subtract($occurred->funds());
        $this->accounts[$toAccount]['balance'] =
            $this->accounts[$toAccount]['balance']->add($occurred->funds());
    }

    public function recordThatAccountWasFrozen(AccountFrozen $occurred): void
    {
        $accountNumber = (string)$occurred->accountNumber();
        $this->accounts[$accountNumber]['isFrozen'] = true;
    }
}
