<?php

namespace lokothodida\Bank\Domain\Event;

use DateTimeInterface;
use lokothodida\Bank\Domain\Event;
use lokothodida\Bank\Domain\Money;

final class FundsDeposited extends Event
{
    private Money $funds;
    private DateTimeInterface $depositedAt;

    public function __construct(Money $funds, DateTimeInterface $depositedAt)
    {
        $this->funds = $funds;
        $this->depositedAt = $depositedAt;
    }

    public function funds(): Money
    {
        return $this->funds;
    }

    public function occurredAt(): DateTimeInterface
    {
        return $this->depositedAt;
    }
}
