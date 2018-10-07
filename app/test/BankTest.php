<?php

use PHPUnit\Framework\TestCase;
use lokothodida\Bank\Bank;
use lokothodida\Bank\Clocks\AlwaysSameTimeClock;
use lokothodida\Bank\Vaults\InMemoryVault;
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
use DateTimeImmutable as TimeStamp;

final class BankTest extends TestCase
{
    private $bank;
    private $vault;

    public function setUp()
    {
        $this->bank = new Bank(
            $this->vault = new InMemoryVault(),
            new AlwaysSameTimeClock(new TimeStamp('2017-01-01'))
        );
    }

    public function testAllowsNewAccountsToBeOpened()
    {
        $accountNumber = $this->bank->openAccount();

        $this->assertSame('10000000', $accountNumber);
        $this->assertEquals(
            new Account(new AccountNumber('10000000'), new Version(1), Money::Gbp(0), false),
            $this->vault->findAccountByAccountNumber(new AccountNumber('10000000'))
        );
    }

    public function testAllowsExistingAccountsToBeDepositedInto()
    {
        $this->vault->recordThatAccountWasOpened(new AccountOpened(
            new AccountNumber('12345678'),
            new Version(1),
            new TimeStamp('2000-01-01')
        ));
        $this->bank->depositIntoAccount('12345678', 500);

        $this->assertEquals(
            new Account(new AccountNumber('12345678'), new Version(1), Money::Gbp(500), false),
            $this->vault->findAccountByAccountNumber(new AccountNumber('12345678'))
        );
    }

    public function testAllowsExistingAccountsToBeWithdrawnFrom()
    {
        $this->vault->recordThatAccountWasOpened(new AccountOpened(
            new AccountNumber('12345678'),
            new Version(1),
            new TimeStamp('2007-08-31')
        ));
        $this->vault->recordThatFundsWereDeposited(new FundsDepositedIntoAccount(
            new AccountNumber('12345678'),
            new Version(2),
            Money::Gbp(10000),
            new TimeStamp('2007-09-18')
        ));
        $this->bank->withdrawFromAccount('12345678', 6000);

        $this->assertEquals(
            new Account(new AccountNumber('12345678'), new Version(1), Money::Gbp(4000), false),
            $this->vault->findAccountByAccountNumber(new AccountNumber('12345678'))
        );
    }

    public function testAllowsForTransfersBetweenExistingAccounts()
    {
        $this->vault->recordThatAccountWasOpened(new AccountOpened(
            new AccountNumber('12345678'),
            new Version(1),
            new TimeStamp('2003-12-01')
        ));
        $this->vault->recordThatAccountWasOpened(new AccountOpened(
            new AccountNumber('87654321'),
            new Version(1),
            new TimeStamp('2004-06-10')
        ));
        $this->vault->recordThatFundsWereDeposited(new FundsDepositedIntoAccount(
            new AccountNumber('12345678'),
            new Version(2),
            Money::Gbp(3500),
            new TimeStamp('2007-09-18')
        ));
        $this->vault->recordThatFundsWereDeposited(new FundsDepositedIntoAccount(
            new AccountNumber('87654321'),
            new Version(2),
            Money::Gbp(1000),
            new TimeStamp('2007-09-18')
        ));
        $this->bank->transferToAccount('12345678', '87654321', 3000);

        $this->assertEquals(
            new Account(new AccountNumber('12345678'), new Version(1), Money::Gbp(500), false),
            $this->vault->findAccountByAccountNumber(new AccountNumber('12345678'))
        );
        $this->assertEquals(
            new Account(new AccountNumber('87654321'), new Version(1), Money::Gbp(4000), false),
            $this->vault->findAccountByAccountNumber(new AccountNumber('87654321'))
        );
    }

    public function testAllowsAccountsToBeFrozen()
    {
        $this->vault->recordThatAccountWasOpened(new AccountOpened(
            new AccountNumber('12345678'),
            new Version(1),
            new TimeStamp('2005-02-29')
        ));
        $this->bank->freezeAccount('12345678');

        $this->assertEquals(
            new Account(new AccountNumber('12345678'), new Version(1), Money::Gbp(0), true),
            $this->vault->findAccountByAccountNumber(new AccountNumber('12345678'))
        );
    }
}
