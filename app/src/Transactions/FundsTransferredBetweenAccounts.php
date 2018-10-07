<?php

namespace lokothodida\Bank\Transactions;

use lokothodida\Bank\Transaction;
use lokothodida\Bank\AccountNumber;
use lokothodida\Bank\Money;
use lokothodida\Bank\Version;
use DateTimeImmutable as TimeStamp;

final class FundsTransferredBetweenAccounts implements Transaction
{
    private $fromAccountNumber;
    private $fromVersion;
    private $toAccountNumber;
    private $toVersion;
    private $funds;
    private $at;

    public function __construct(
        AccountNumber $fromAccountNumber,
        Version $fromVersion,
        AccountNumber $toAccountNumber,
        Version $toVersion,
        Money $funds,
        TimeStamp $at
    ) {
        $this->fromAccountNumber = $fromAccountNumber;
        $this->fromVersion = $fromVersion;
        $this->toAccountNumber = $toAccountNumber;
        $this->toVersion = $toVersion;
        $this->funds = $funds;
        $this->at = $at;
    }

    public function fromAccountNumber(): AccountNumber
    {
        return $this->fromAccountNumber;
    }

    public function fromVersion(): Version
    {
        return $this->fromVersion;
    }

    public function toAccountNumber(): AccountNumber
    {
        return $this->toAccountNumber;
    }

    public function toVersion(): Version
    {
        return $this->toVersion;
    }

    public function funds(): Money
    {
        return $this->funds;
    }

    public function at(): TimeStamp
    {
        return $this->at;
    }
}
