<?php

namespace lokothodida\Bank\Domain\Event;

use DateTimeInterface;
use lokothodida\Bank\Domain\Event;

final class AccountClosed extends Event
{
    private DateTimeInterface $frozenAt;

    public function __construct(DateTimeInterface $frozenAt)
    {
        $this->frozenAt = $frozenAt;
    }
}
