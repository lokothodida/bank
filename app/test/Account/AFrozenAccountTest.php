<?php

use PHPUnit\Framework\TestCase;
use lokothodida\Bank\Account;
use lokothodida\Bank\AccountNumber;
use lokothodida\Bank\Money;
use lokothodida\Bank\Version;
use DateTimeImmutable as TimeStamp;

final class AFrozenAccountTest extends TestCase
{
    /**
     * @expectedException DomainException
     */
    public function testCannotBeDepositedInto()
    {
        (new Account(
            new AccountNumber('12345678'),
            $version = new Version(1),
            Money::Gbp(0),
            $isFrozen = true
        ))->deposit(Money::Gbp(1000), new TimeStamp('2017-01-01'));
    }

    /**
     * @expectedException DomainException
     */
    public function testCannotBeWithdrawnFrom()
    {
        (new Account(
            new AccountNumber('12345678'),
            new Version(1),
            Money::Gbp(2000),
            true
        ))->withdraw(Money::Gbp(1000), new TimeStamp('2009-04-04'));
    }

    /**
     * @expectedException DomainException
     */
    public function testCannotTransferFundsToOtherAccounts()
    {
        $from = new Account(new AccountNumber('12345678'), new Version(1), Money::Gbp(1500), true);
        $to = new Account(new AccountNumber('02345678'), new Version(1), Money::Gbp(10000), false);
        $from->transfer(Money::Gbp(1000), $to, new TimeStamp('2014-07-18'));
    }

    /**
     * @expectedException DomainException
     */
    public function testCannotHaveFundsTransferredInFromOtherAccounts()
    {
        $from = new Account(new AccountNumber('12345678'), new Version(1), Money::Gbp(1500), false);
        $to = new Account(new AccountNumber('02345678'), new Version(1), Money::Gbp(10000), true);
        $from->transfer(Money::Gbp(1000), $to, new TimeStamp('2014-07-18'));
    }
}
