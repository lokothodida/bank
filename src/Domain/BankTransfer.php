<?php

namespace lokothodida\Bank\Domain;

use DateTimeInterface;
use lokothodida\Bank\DepositIntoAccount;
use lokothodida\Bank\Domain\Event\BankTransferCancelled;
use lokothodida\Bank\Domain\Event\BankTransferCompleted;
use lokothodida\Bank\Domain\Event\BankTransferInitiated;
use lokothodida\Bank\Domain\Event\FailedToTransferFundsIn;
use lokothodida\Bank\Domain\Event\FundsTransferredOut;
use lokothodida\Bank\WithdrawFromAccount;

final class BankTransfer
{
    /**
     * @var Event[]
     */
    private array $events;

    private function __construct(Event ...$events)
    {
        $this->events = $events;
    }

    public static function initiate(
        string $transferId,
        string $senderAccountId,
        string $recipientAccountId,
        Money $funds,
        DateTimeInterface $time
    ): BankTransfer {
        return new BankTransfer(
            new BankTransferInitiated($transferId, $senderAccountId, $recipientAccountId, $funds, $time)
        );
    }

    /**
     * @return Event[]
     */
    public function events(): array
    {
        return array_reverse($this->events);
    }

    public function transferOut(WithdrawFromAccount $withdraw, Clock $clock): BankTransfer
    {
        $initiated = $this->initiated();
        $withdraw($initiated->senderAccountId(), $initiated->funds()->amount());
        return new BankTransfer(
            new FundsTransferredOut($initiated->transferId(), $clock->now()),
            ...$this->events
        );
    }

    public function transferIn(DepositIntoAccount $deposit, Clock $clock): BankTransfer
    {
        $initiated = $this->initiated();
        try {
            $deposit($initiated->recipientAccountId(), $initiated->funds()->amount());

            return new BankTransfer(
                new BankTransferCompleted($initiated->transferId(), $clock->now()),
                ...$this->events
            );
        } catch (\Exception $e) {
            return new BankTransfer(
                new FailedToTransferFundsIn($initiated->transferId(), $clock->now()),
                ...$this->events
            );
        }
    }

    public function refund(DepositIntoAccount $deposit, Clock $clock): BankTransfer
    {
        $initiated = $this->initiated();
        $deposit($initiated->senderAccountId(), $initiated->funds()->amount());

        return new BankTransfer(
            new BankTransferCancelled($initiated->transferId(), $clock->now()),
            ...$this->events
        );
    }

    private function initiated(): BankTransferInitiated
    {
        /** @var BankTransferInitiated */
        return $this->events[count($this->events) - 1];
    }
}
