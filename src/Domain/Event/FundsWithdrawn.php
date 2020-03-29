<?php


namespace lokothodida\Bank\Domain\Event;

use DateTimeInterface;
use lokothodida\Bank\Domain\Event;
use lokothodida\Bank\Domain\Money;

final class FundsWithdrawn extends Event
{
    private Money $funds;
    private DateTimeInterface $withdrawnAt;

    public function __construct(Money $funds, DateTimeInterface $withdrawnAt)
    {
        $this->funds = $funds;
        $this->withdrawnAt = $withdrawnAt;
    }

    public function funds(): Money
    {
        return $this->funds;
    }

    public function occurredAt(): DateTimeInterface
    {
        return $this->withdrawnAt;
    }
}
