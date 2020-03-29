<?php

namespace lokothodida\Bank\Domain\Event;

use DateTimeInterface;
use lokothodida\Bank\Domain\Event;
use lokothodida\Bank\Domain\Money;

final class FundsTransferredOut extends Event
{
    private string $senderAccountId;
    private string $recipientAccountId;
    private Money $funds;
    private DateTimeInterface $occurredAt;

    public function __construct(string $senderAccountId, string $recipientAccountId, Money $funds, DateTimeInterface $occurredAt)
    {
        $this->senderAccountId = $senderAccountId;
        $this->recipientAccountId = $recipientAccountId;
        $this->funds = $funds;
        $this->occurredAt = $occurredAt;
    }

    public function senderAccountId(): string
    {
        return $this->senderAccountId;
    }

    public function recipientAccountId(): string
    {
        return $this->recipientAccountId;
    }

    public function funds(): Money
    {
        return $this->funds;
    }

    public function occurredAt(): DateTimeInterface
    {
        return $this->occurredAt;
    }
}
