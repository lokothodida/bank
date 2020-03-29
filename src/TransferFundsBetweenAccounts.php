<?php

namespace lokothodida\Bank;

use lokothodida\Bank\Domain\BankTransfer;
use lokothodida\Bank\Domain\BankTransferRepository;
use lokothodida\Bank\Domain\Clock;
use lokothodida\Bank\Domain\Event\BankTransferInitiated;
use lokothodida\Bank\Domain\Event\FailedToTransferFundsIn;
use lokothodida\Bank\Domain\Event\FundsTransferredOut;
use lokothodida\Bank\Domain\EventPublisher;
use lokothodida\Bank\Domain\Money;

final class TransferFundsBetweenAccounts
{
    private WithdrawFromAccount $withdraw;
    private DepositIntoAccount $deposit;
    private Clock $clock;
    private EventPublisher $publisher;
    private BankTransferRepository $transfers;

    public function __construct(
        WithdrawFromAccount $withdraw,
        DepositIntoAccount $deposit,
        Clock $clock,
        EventPublisher $publisher,
        BankTransferRepository $transfers
    ) {
        $this->withdraw = $withdraw;
        $this->deposit = $deposit;
        $this->clock = $clock;
        $this->publisher = $publisher;
        $this->transfers = $transfers;
        $this->declareProcess();
    }

    public function __invoke(string $senderAccountId, string $recipientAccountId, int $amount): void
    {
        $this->transfers->set(
            $transferId = $this->transfers->newTransferId(),
            BankTransfer::initiate($transferId, $senderAccountId, $recipientAccountId, new Money($amount), $this->clock->now())
        );
    }

    private function declareProcess(): void
    {
        $this->publisher->on(BankTransferInitiated::class, function (BankTransferInitiated $event) {
            $transfer = $this->transfers->get($event->transferId());
            $this->transfers->set(
                $event->transferId(),
                $transfer->transferOut($this->withdraw, $this->clock)
            );
        });
        $this->publisher->on(FundsTransferredOut::class, function (FundsTransferredOut $event) {
            $transfer = $this->transfers->get($event->transferId());
            $this->transfers->set(
                $event->transferId(),
                $transfer->transferIn($this->deposit, $this->clock)
            );
        });
        $this->publisher->on(FailedToTransferFundsIn::class, function (FailedToTransferFundsIn $event) {
            $transfer = $this->transfers->get($event->transferId());
            $this->transfers->set(
                $event->transferId(),
                $transfer->refund($this->deposit, $this->clock)
            );
        });
    }
}
