<?php

namespace lokothodida\Bank\Transactions;

use lokothodida\Bank\Transaction;
use lokothodida\Bank\AccountNumber;
use lokothodida\Bank\Version;
use DateTimeImmutable as TimeStamp;

final class AccountOpened implements Transaction
{
    private $accountNumber;
    private $version;
    private $at;

    public function __construct(AccountNumber $accountNumber, Version $version, TimeStamp $at)
    {
        $this->accountNumber = $accountNumber;
        $this->version = $version;
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

    public function at(): TimeStamp
    {
        return $this->at;
    }
}
