<?php

namespace lokothodida\Bank\Domain;

use DateTimeInterface;
use DomainException;
use lokothodida\Bank\Domain\Event\AccountClosed;
use lokothodida\Bank\Domain\Event\AccountFrozen;
use lokothodida\Bank\Domain\Event\AccountOpened;
use lokothodida\Bank\Domain\Event\AccountUnfrozen;
use lokothodida\Bank\Domain\Event\FundsDeposited;
use lokothodida\Bank\Domain\Event\FundsWithdrawn;

final class Account
{
    /** @var Event[] */
    private array $history;

    public function __construct(Event ...$history)
    {
        $this->history = $history;
    }

    public static function open(string $accountId, string $customerId, DateTimeInterface $time): Account
    {
        return new Account(new AccountOpened($accountId, $customerId, $time));
    }

    public static function withHistory(Event ...$history): Account
    {
        return new Account(...array_reverse($history));
    }

    public function deposit(Money $funds, DateTimeInterface $time): Account
    {
        if ($this->isClosed()) {
            throw new DomainException('Account closed');
        }

        return new Account(
            new FundsDeposited($funds, $time),
            ...$this->history,
        );
    }

    public function withdraw(Money $funds, DateTimeInterface $time): Account
    {
        if ($this->isClosed()) {
            throw new DomainException('Account closed');
        }

        if ($this->isFrozen()) {
            throw new DomainException('Account frozen');
        }

        if ($this->balance()->lessThan($funds)) {
            throw new DomainException('Insufficient funds');
        }

        return new Account(
            new FundsWithdrawn($funds, $time),
            ...$this->history,
        );
    }

    public function freeze(DateTimeInterface $time): Account
    {
        if ($this->isClosed()) {
            throw new DomainException('Account closed');
        }

        return new Account(
            new AccountFrozen($time),
            ...$this->history
        );
    }

    public function unfreeze(DateTimeInterface $time): Account
    {
        if ($this->isClosed()) {
            throw new DomainException('Account closed');
        }

        return new Account(
            new AccountUnfrozen($time),
            ...$this->history
        );
    }

    public function close(DateTimeInterface $time): Account
    {
        return new Account(
            new AccountClosed($time),
            ...$this->history
        );
    }

    private function balance(): Money
    {
        return array_reduce(
            array_reverse($this->history),
            function(Money $sum, Event $event): Money {
                switch (get_class($event)) {
                    case FundsDeposited::class:
                        return $sum->add($event->funds());
                    case FundsWithdrawn::class:
                        return $sum->subtract($event->funds());
                    default:
                        return $sum;
                }
            },
            new Money(0)
        );
    }

    private function isFrozen(): bool
    {
        return array_reduce(
            array_reverse($this->history),
            function(bool $isFrozen, Event $event): bool {
                switch (get_class($event)) {
                    case AccountFrozen::class:
                        return true;
                    default:
                        return $isFrozen;
                }
            },
            false
        );
    }

    private function isClosed(): bool
    {
        return array_reduce(
            array_reverse($this->history),
            function(bool $isClosed, Event $event): bool {
                switch (get_class($event)) {
                    case AccountClosed::class:
                        return true;
                    default:
                        return $isClosed;
                }
            },
            false
        );
    }
}
