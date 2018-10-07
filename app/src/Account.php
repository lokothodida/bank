<?php

namespace lokothodida\Bank;

use lokothodida\Bank\Transactions\{
    AccountOpened,
    FundsDepositedIntoAccount,
    FundsWithdrawnFromAccount,
    AccountFrozen,
    FundsTransferredBetweenAccounts
};
use DomainException;
use DateTimeImmutable as TimeStamp;

final class Account
{
    private $accountNumber;
    private $version;
    private $balance;
    private $isFrozen;

    public function __construct(AccountNumber $accountNumber, Version $version, Money $balance, bool $isFrozen)
    {
        $this->accountNumber = $accountNumber;
        $this->version = $version;
        $this->balance = $balance;
        $this->isFrozen = $isFrozen;
    }

    public static function open(AccountNumber $accountNumber, TimeStamp $at): AccountOpened
    {
        return new AccountOpened($accountNumber, new Version(1), $at);
    }

    public function deposit(Money $funds, TimeStamp $at): FundsDepositedIntoAccount
    {
        $this->mustNotBeFrozen();

        return new FundsDepositedIntoAccount($this->accountNumber, $this->version->next(), $funds, $at);
    }

    public function withdraw(Money $funds, TimeStamp $at): FundsWithdrawnFromAccount
    {
        $this->mustNotBeFrozen();
        $this->mustHaveSufficientFunds($funds);

        return new FundsWithdrawnFromAccount($this->accountNumber, $this->version->next(), $funds, $at);
    }

    public function freeze(TimeStamp $at): AccountFrozen
    {
        $this->mustNotBeFrozen();

        return new AccountFrozen($this->accountNumber, $this->version->next(), $at);
    }

    public function transfer(Money $funds, Account $recipient, TimeStamp $at): FundsTransferredBetweenAccounts
    {
        $this->mustNotBeFrozen();
        $this->mustHaveSufficientFunds($funds);
        $recipient->mustNotBeFrozen();

        return new FundsTransferredBetweenAccounts(
            $this->accountNumber,
            $this->version->next(),
            $recipient->accountNumber,
            $recipient->version->next(),
            $funds,
            $at
        );
    }

    private function mustNotBeFrozen(): void
    {
        if ($this->isFrozen) {
            throw new DomainException('Account is frozen');
        }
    }

    private function mustHaveSufficientFunds(Money $funds): void
    {
        if ($this->balance->lessThan($funds)) {
            throw new DomainException('Insufficient funds');
        }
    }
}
