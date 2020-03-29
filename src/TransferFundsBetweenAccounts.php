<?php

namespace lokothodida\Bank;

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

    public function __construct(
        WithdrawFromAccount $withdraw,
        DepositIntoAccount $deposit,
        Clock $clock,
        EventPublisher $publisher
    ) {
        $this->withdraw = $withdraw;
        $this->deposit = $deposit;
        $this->clock = $clock;
        $this->publisher = $publisher;
        $this->declareProcess();
    }

    public function __invoke(string $senderAccountId, string $recipientAccountId, int $amount): void
    {
        $this->publisher->publish(new BankTransferInitiated(
            $senderAccountId,
            $recipientAccountId,
            new Money($amount),
            $this->clock->now()
        ));
    }

    private function declareProcess(): void
    {
        $this->publisher->on(BankTransferInitiated::class, function (BankTransferInitiated $event) {
            $this->transferFundsOut($event->senderAccountId(), $event->funds()->amount());
            $this->publisher->publish(new FundsTransferredOut(
                $event->senderAccountId(),
                $event->recipientAccountId(),
                $event->funds(),
                $this->clock->now()
            ));
        });
        $this->publisher->on(FundsTransferredOut::class, function (FundsTransferredOut $event) {
            try {
                $this->transferFundsIn($event->recipientAccountId(), $event->funds()->amount());
            } catch (\Exception $e) {
                $this->publisher->publish(new FailedToTransferFundsIn(
                    $event->senderAccountId(),
                    $event->recipientAccountId(),
                    $event->funds(),
                    $this->clock->now()
                ));
            }
        });
        $this->publisher->on(FailedToTransferFundsIn::class, function (FailedToTransferFundsIn $event) {
            $this->refundSender($event->senderAccountId(), $event->funds()->amount());
        });
    }

    private function transferFundsOut(string $senderAccountId, int $amount): void
    {
        ($this->withdraw)($senderAccountId, $amount);
    }

    private function transferFundsIn(string $recipientAccountId, int $amount): void
    {
        ($this->deposit)($recipientAccountId, $amount);
    }

    private function refundSender(string $senderAccountId, int $amount): void
    {
        ($this->deposit)($senderAccountId, $amount);
    }
}
