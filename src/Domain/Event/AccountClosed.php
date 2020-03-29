<?php

namespace lokothodida\Bank\Domain\Event;

use DateTimeInterface;
use lokothodida\Bank\Domain\Event;

final class AccountClosed extends Event
{
    private DateTimeInterface $closedAt;

    public function __construct(DateTimeInterface $closedAt)
    {
        $this->closedAt = $closedAt;
    }

    public function occurredAt(): DateTimeInterface
    {
        return $this->closedAt;
    }
}
