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
        ($this->withdraw)($senderAccountId, $amount);
        ($this->deposit)($recipientAccountId, $amount);
    }
}
