<?php

use lokothodida\Bank\Domain\Account;
use lokothodida\Bank\Domain\Money;
use PHPUnit\Framework\TestCase;

final class ClosedAccountTest extends TestCase
{
    public function testCannotBeDepositedInto(): void
    {
        $this->expectExceptionMessage('Account closed');
        Account::open('account-id', 'customer-id', new DateTimeImmutable())
            ->close(new DateTimeImmutable())
            ->deposit(new Money(100), new DateTimeImmutable());
    }

    public function testCannotBeWithdrawnFrom(): void
    {
        $this->expectExceptionMessage('Account closed');
        Account::open('account-id', 'customer-id', new DateTimeImmutable())
            ->close(new DateTimeImmutable())
            ->withdraw(new Money(100), new DateTimeImmutable());
    }

    public function testCannotBeFrozen(): void
    {
        $this->expectExceptionMessage('Account closed');
        Account::open('account-id', 'customer-id', new DateTimeImmutable())
            ->close(new DateTimeImmutable())
            ->freeze(new DateTimeImmutable());
    }

    public function testCannotBeUnfrozen(): void
    {
        $this->expectExceptionMessage('Account closed');
        Account::open('account-id', 'customer-id', new DateTimeImmutable())
            ->freeze(new DateTimeImmutable())
            ->close(new DateTimeImmutable())
            ->unfreeze(new DateTimeImmutable());
    }
}
