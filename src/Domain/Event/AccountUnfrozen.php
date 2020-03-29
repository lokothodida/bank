<?php


namespace lokothodida\Bank\Domain\Event;

use DateTimeInterface;
use lokothodida\Bank\Domain\Event;

final class AccountUnfrozen extends Event
{
    private DateTimeInterface $unfrozenAt;

    public function __construct(DateTimeInterface $unfrozenAt)
    {
        $this->unfrozenAt = $unfrozenAt;
    }

    public function occurredAt(): DateTimeInterface
    {
        return $this->unfrozenAt;
    }
}
