<?php

namespace lokothodida\Bank\Infrastructure\Clock;

use DateTimeImmutable;
use DateTimeInterface;
use lokothodida\Bank\Domain\Clock;

final class LocalClock implements Clock
{
    public function now(): DateTimeInterface
    {
        return new DateTimeImmutable();
    }
}
