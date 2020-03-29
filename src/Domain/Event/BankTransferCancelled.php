<?php


namespace lokothodida\Bank\Domain\Event;

use DateTimeInterface;
use lokothodida\Bank\Domain\Event;
use lokothodida\Bank\Domain\Money;

class BankTransferCancelled extends Event
{
    private string $transferId;
    private DateTimeInterface $occurredAt;

    public function __construct(string $transferId, DateTimeInterface $occurredAt)
    {
        $this->transferId = $transferId;
        $this->occurredAt = $occurredAt;
    }

    public function transferId(): string
    {
        return $this->transferId;
    }

    public function occurredAt(): DateTimeInterface
    {
        return $this->occurredAt;
    }
}
