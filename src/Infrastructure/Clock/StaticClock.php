<?php

namespace lokothodida\Bank\Infrastructure\Clock;

use DateTimeInterface;
use lokothodida\Bank\Domain\Clock;

final class StaticClock implements Clock
{
    private DateTimeInterface $now;

    public function __construct(DateTimeInterface $now)
    {
        $this->now = $now;
    }

    public function now(): DateTimeInterface
    {
        return $this->now;
    }
}
