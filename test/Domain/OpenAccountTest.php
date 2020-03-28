<?php

namespace lokothodida\BankTest\Domain;

use DateTimeImmutable;
use lokothodida\Bank\Domain\Account;
use lokothodida\Bank\Domain\Event\AccountFrozen;
use lokothodida\Bank\Domain\Event\AccountOpened;
use lokothodida\Bank\Domain\Event\FundsDeposited;
use lokothodida\Bank\Domain\Event\FundsWithdrawn;
use lokothodida\Bank\Domain\Money;
use PHPUnit\Framework\TestCase;

final class OpenAccountTest extends TestCase
{
    public function testCanBeDepositedInto(): void
    {
        $account = Account::open('account-id', 'customer-id', $openedAt = new DateTimeImmutable());
        $deposited = $account->deposit(new Money(100), $depositedAt = new DateTimeImmutable());

        $this->assertEquals(
            Account::withHistory(
                new AccountOpened('account-id', 'customer-id', $openedAt),
                new FundsDeposited(new Money(100), $depositedAt)
            ),
            $deposited
        );
    }

    public function testCanBeWithdrawnFromWhenSufficientFundsExist(): void
    {
        $account = Account::open('account-id', 'customer-id', $openedAt = new DateTimeImmutable());
        $withdrawn = $account
            ->deposit(new Money(100), $depositedAt = new DateTimeImmutable())
            ->withdraw(new Money(50), $withdrawnAt = new DateTimeImmutable());

        $this->assertEquals(
            Account::withHistory(
                new AccountOpened('account-id', 'customer-id', $openedAt),
                new FundsDeposited(new Money(100), $depositedAt),
                new FundsWithdrawn(new Money(50), $withdrawnAt),
            ),
            $withdrawn
        );
    }

    public function testCannotBeWithdrawnFromWhenInsufficientFundsExist(): void
    {
        $this->expectExceptionMessage('Insufficient funds');

        Account::open('account-id', 'customer-id', new DateTimeImmutable())
            ->deposit(new Money(100), new DateTimeImmutable())
            ->withdraw(new Money(101), new DateTimeImmutable());
    }

    public function testCannotBeClosedWhenBalanceIsNonZero(): void
    {
        $this->expectExceptionMessage('Non-zero balance');

        Account::open('account-id', 'customer-id', new DateTimeImmutable())
            ->deposit(new Money(100), new DateTimeImmutable())
            ->close(new DateTimeImmutable());
    }

    public function testCanBeFrozen(): void
    {
        $account = Account::open('account-id', 'customer-id', $openedAt = new DateTimeImmutable());
        $frozen = $account
            ->deposit(new Money(100), $depositedAt = new DateTimeImmutable())
            ->freeze($frozenAt = new DateTimeImmutable());

        $this->assertEquals(
            Account::withHistory(
                new AccountOpened('account-id', 'customer-id', $openedAt),
                new FundsDeposited(new Money(100), $depositedAt),
                new AccountFrozen($frozenAt),
            ),
            $frozen
        );
    }
}
