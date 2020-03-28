<?php

use lokothodida\Bank\Domain\Account;
use lokothodida\Bank\Domain\Event\AccountFrozen;
use lokothodida\Bank\Domain\Event\AccountOpened;
use lokothodida\Bank\Domain\Event\AccountUnfrozen;
use lokothodida\Bank\Domain\Event\FundsDeposited;
use lokothodida\Bank\Domain\Money;
use PHPUnit\Framework\TestCase;

final class FrozenAccountTest extends TestCase
{
    public function testCanBeDepositedInto(): void
    {
        $account = Account::open('account-id', 'customer-id', $openedAt = new DateTimeImmutable());
        $deposited = $account
            ->freeze($frozenAt = new DateTimeImmutable())
            ->deposit(new Money(100), $depositedAt = new DateTimeImmutable());

        $this->assertEquals(
            Account::withHistory(
                new AccountOpened('account-id', 'customer-id', $openedAt),
                new AccountFrozen($frozenAt),
                new FundsDeposited(new Money(100), $depositedAt)
            ),
            $deposited
        );
    }

    public function testCannotBeWithdrawnFrom(): void
    {
        $this->expectExceptionMessage('Account frozen');
        Account::open('account-id', 'customer-id', new DateTimeImmutable())
            ->freeze(new DateTimeImmutable())
            ->deposit(new Money(100), new DateTimeImmutable())
            ->withdraw(new Money(1), new DateTimeImmutable());
    }

    public function testCannotBeReFrozen(): void
    {
        $this->expectExceptionMessage('Account already frozen');
        Account::open('account-id', 'customer-id', new DateTimeImmutable())
            ->freeze(new DateTimeImmutable())
            ->freeze(new DateTimeImmutable());
    }

    public function testCanBeUnfrozen(): void
    {
        $account = Account::open('account-id', 'customer-id', $openedAt = new DateTimeImmutable());
        $unfrozen = $account
            ->freeze($frozenAt = new DateTimeImmutable())
            ->deposit(new Money(100), $depositedAt = new DateTimeImmutable())
            ->unfreeze($unfrozenAt = new DateTimeImmutable());

        $this->assertEquals(
            Account::withHistory(
                new AccountOpened('account-id', 'customer-id', $openedAt),
                new AccountFrozen($frozenAt),
                new FundsDeposited(new Money(100), $depositedAt),
                new AccountUnfrozen($unfrozenAt)
            ),
            $unfrozen
        );
    }
}
