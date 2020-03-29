<?php

namespace lokothodida\BankTest;

use DateTimeImmutable;
use lokothodida\Bank\CloseAccount;
use lokothodida\Bank\DepositIntoAccount;
use lokothodida\Bank\FreezeAccount;
use lokothodida\Bank\OpenAccount;
use lokothodida\Bank\UnfreezeAccount;
use lokothodida\Bank\WithdrawFromAccount;
use lokothodida\Bank\Domain\Account;
use lokothodida\Bank\Domain\Event\AccountClosed;
use lokothodida\Bank\Domain\Event\AccountFrozen;
use lokothodida\Bank\Domain\Event\AccountOpened;
use lokothodida\Bank\Domain\Event\AccountUnfrozen;
use lokothodida\Bank\Domain\Event\FundsDeposited;
use lokothodida\Bank\Domain\Event\FundsWithdrawn;
use lokothodida\Bank\Domain\Money;
use lokothodida\Bank\Infrastructure\Clock\StaticClock;
use lokothodida\Bank\Infrastructure\Storage\InMemoryAccountRepository;
use PHPUnit\Framework\TestCase;

final class ManagingAccountsTest extends TestCase
{
    public function testNewAccountsCanBeOpened(): void
    {
        $accounts = new InMemoryAccountRepository();
        $clock = new StaticClock($now = new DateTimeImmutable());
        $command = new OpenAccount($accounts, $clock);
        $accountId = $command('a-customer-id');

        $this->assertSame('0', $accountId);
        $this->assertEquals(
            Account::withHistory(new AccountOpened('0', 'a-customer-id', $now)),
            $accounts->get('0')
        );
    }

    public function testExistingAccountsCanBeDepositedInto(): void
    {
        $accounts = new InMemoryAccountRepository();
        $accountId = 'account-1';
        $accounts->set($accountId, Account::open($accountId, 'customer-id', $opened = new DateTimeImmutable()));
        $clock = new StaticClock($now = new DateTimeImmutable());
        $command = new DepositIntoAccount($accounts, $clock);
        $command($accountId, 100);

        $this->assertEquals(
            Account::withHistory(
                new AccountOpened($accountId, 'customer-id', $opened),
                new FundsDeposited(new Money(100), $now),
            ),
            $accounts->get($accountId)
        );
    }

    public function testExistingAccountsCanBeWithdrawnFrom(): void
    {
        $accounts = new InMemoryAccountRepository();
        $accountId = 'account-1';
        $accounts->set(
            $accountId,
            Account::open($accountId, 'customer-id', $opened = new DateTimeImmutable())
                ->deposit(new Money(100), $deposited = new DateTimeImmutable())
        );
        $clock = new StaticClock($now = new DateTimeImmutable());
        $command = new WithdrawFromAccount($accounts, $clock);
        $command($accountId, 10);

        $this->assertEquals(
            Account::withHistory(
                new AccountOpened($accountId, 'customer-id', $opened),
                new FundsDeposited(new Money(100), $deposited),
                new FundsWithdrawn(new Money(10), $now)
            ),
            $accounts->get($accountId)
        );
    }

    public function testExistingAccountsCanBeFrozen(): void
    {
        $accounts = new InMemoryAccountRepository();
        $accountId = 'account-1';
        $accounts->set(
            $accountId,
            Account::open($accountId, 'customer-id', $opened = new DateTimeImmutable())
        );
        $clock = new StaticClock($now = new DateTimeImmutable());
        $command = new FreezeAccount($accounts, $clock);
        $command($accountId);

        $this->assertEquals(
            Account::withHistory(
                new AccountOpened($accountId, 'customer-id', $opened),
                new AccountFrozen($now)
            ),
            $accounts->get($accountId)
        );
    }

    public function testExistingAccountsCanBeUnfrozen(): void
    {
        $accounts = new InMemoryAccountRepository();
        $accountId = 'account-1';
        $accounts->set(
            $accountId,
            Account::open($accountId, 'customer-id', $opened = new DateTimeImmutable())
                ->freeze($frozenAt = new DateTimeImmutable())
        );
        $clock = new StaticClock($now = new DateTimeImmutable());
        $command = new UnfreezeAccount($accounts, $clock);
        $command($accountId);

        $this->assertEquals(
            Account::withHistory(
                new AccountOpened($accountId, 'customer-id', $opened),
                new AccountFrozen($frozenAt),
                new AccountUnfrozen($now)
            ),
            $accounts->get($accountId)
        );
    }

    public function testExistingAccountsCanBeClosed(): void
    {
        $accounts = new InMemoryAccountRepository();
        $accountId = 'account-1';
        $accounts->set(
            $accountId,
            Account::open($accountId, 'customer-id', $opened = new DateTimeImmutable())
        );
        $clock = new StaticClock($now = new DateTimeImmutable());
        $command = new CloseAccount($accounts, $clock);
        $command($accountId);

        $this->assertEquals(
            Account::withHistory(
                new AccountOpened($accountId, 'customer-id', $opened),
                new AccountClosed($now)
            ),
            $accounts->get($accountId)
        );
    }
}
