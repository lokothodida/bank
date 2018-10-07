<?php

namespace lokothodida\Bank;

use lokothodida\Bank\Transactions\{
    AccountOpened,
    FundsDepositedIntoAccount,
    FundsWithdrawnFromAccount,
    AccountFrozen,
    FundsTransferredBetweenAccounts
};

interface Vault
{
    public function generateAccountNumber(): AccountNumber;

    public function findAccountByAccountNumber(AccountNumber $accountNumber): Account;

    public function recordThatAccountWasOpened(AccountOpened $occurred): void;

    public function recordThatFundsWereDeposited(FundsDepositedIntoAccount $occurred): void;

    public function recordThatFundsWereWithdrawn(FundsWithdrawnFromAccount $occurred): void;

    public function recordThatFundsWereTransferred(FundsTransferredBetweenAccounts $occurred): void;

    public function recordThatAccountWasFrozen(AccountFrozen $occurred): void;
}
