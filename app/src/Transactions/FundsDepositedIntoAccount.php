<?php

namespace lokothodida\Bank\Transactions;

use lokothodida\Bank\Transaction;
use lokothodida\Bank\AccountNumber;
use lokothodida\Bank\Money;
use lokothodida\Bank\Version;
use DateTimeImmutable as TimeStamp;

final class FundsDepositedIntoAccount implements Transaction
{
    private $accountNumber;
    private $version;
    private $funds;
    private $at;

    public function __construct(
        AccountNumber $accountNumber,
        Version $version,
        Money $funds,
        TimeStamp $at
    ) {
        $this->accountNumber = $accountNumber;
        $this->version = $version;
        $this->funds = $funds;
        $this->at = $at;
    }

    public function accountNumber(): AccountNumber
    {
        return $this->accountNumber;
    }

    public function version(): Version
    {
        return $this->version;
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
