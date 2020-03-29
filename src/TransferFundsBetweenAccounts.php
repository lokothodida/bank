<?php

namespace lokothodida\Bank;

final class TransferFundsBetweenAccounts
{
    private WithdrawFromAccount $withdraw;
    private DepositIntoAccount $deposit;

    public function __construct(WithdrawFromAccount $withdraw, DepositIntoAccount $deposit)
    {
        $this->withdraw = $withdraw;
        $this->deposit = $deposit;
    }

    public function __invoke(string $senderAccountId, string $recipientAccountId, int $amount): void
    {
        $this->transferFundsOut($senderAccountId, $amount);

        try {
            $this->transferFundsIn($recipientAccountId, $amount);
        } catch (\Exception $e) {
            $this->refundSender($senderAccountId, $amount);
        }
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
