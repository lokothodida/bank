<?php

use PHPUnit\Framework\TestCase;
use lokothodida\Bank\Account;
use lokothodida\Bank\AccountNumber;
use lokothodida\Bank\Money;
use lokothodida\Bank\Version;
use lokothodida\Bank\Transactions\{
    FundsDepositedIntoAccount,
    FundsWithdrawnFromAccount,
    AccountFrozen,
    FundsTransferredBetweenAccounts
};
use DateTimeImmutable as TimeStamp;

final class AnOpenAccountTest extends TestCase
{
    public function testCanHaveFundsDepositedIntoIt()
    {
        $this->assertEquals(
            new FundsDepositedIntoAccount(
                new AccountNumber('12345678'),
                new Version(2),
                Money::Gbp(2500),
                new TimeStamp('2018-05-24')
            ),
            (new Account(new AccountNumber('12345678'), new Version(1), Money::Gbp(0), false))
                ->deposit(Money::Gbp(2500), new TimeStamp('2018-05-24'))
        );
    }

    public function testCanHaveFundsWithdrawnFromIt()
    {
        $this->assertEquals(
            new FundsWithdrawnFromAccount(
                new AccountNumber('12345678'),
                new Version(5),
                Money::Gbp(3000),
                new TimeStamp('2018-12-09')
            ),
            (new Account(new AccountNumber('12345678'), new Version(4), Money::Gbp(3500), false))
                ->withdraw(Money::Gbp(3000), new TimeStamp('2018-12-09'))
        );
    }

    /**
     * @expectedException DomainException
     */
    public function testCannotHaveMoreFundsWithdrawnThanTheCurrentBalance()
    {
        (new Account(new AccountNumber('12345678'), new Version(1), Money::Gbp(3500), false))
                ->withdraw(Money::Gbp(3700), new TimeStamp('2015-02-28'));
    }

    public function testCanBeFrozen()
    {
        $this->assertEquals(
            new AccountFrozen(
                new AccountNumber('12345678'),
                new Version(2),
                new TimeStamp('2012-04-14')
            ),
            (new Account(new AccountNumber('12345678'), new Version(1), Money::Gbp(42), false))
                ->freeze(new TimeStamp('2012-04-14'))
        );
    }

    public function testCanTransferFundsToAnotherOpenAccount()
    {
        $from = new Account(new AccountNumber('12345678'), new Version(6), Money::Gbp(20000), false);
        $to = new Account(new AccountNumber('02345678'), new Version(20), Money::Gbp(10000), false);
        $this->assertEquals(
            new FundsTransferredBetweenAccounts(
                new AccountNumber('12345678'),
                new Version(7),
                new AccountNumber('02345678'),
                new Version(21),
                Money::Gbp(1000),
                new TimeStamp('2014-07-18')
            ),
            $from->transfer(Money::Gbp(1000), $to, new TimeStamp('2014-07-18'))
        );
    }

    /**
     * @expectedException DomainException
     */
    public function testCannotTransferMoreFundsThanTheCurrentBalance()
    {
        $from = new Account(new AccountNumber('12345678'), new Version(1), Money::Gbp(860), false);
        $to = new Account(new AccountNumber('02345678'), new Version(1), Money::Gbp(10000), false);
        $from->transfer(Money::Gbp(1000), $to, new TimeStamp('2014-07-18'));
    }
}
