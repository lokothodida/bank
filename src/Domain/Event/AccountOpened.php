<?php


namespace lokothodida\Bank\Domain\Event;

use DateTimeInterface;
use lokothodida\Bank\Domain\Event;

final class AccountOpened extends Event
{
    private string $accountId;
    private string $customerId;
    private DateTimeInterface $openedAt;

    public function __construct(string $accountId, string $customerId, DateTimeInterface $openedAt)
    {
        $this->accountId = $accountId;
        $this->customerId = $customerId;
        $this->openedAt = $openedAt;
    }

    public function accountId(): string
    {
        return $this->accountId;
    }

    public function customerId(): string
    {
        return $this->customerId;
    }

    public function occurredAt(): DateTimeInterface
    {
        return $this->openedAt;
    }
}
