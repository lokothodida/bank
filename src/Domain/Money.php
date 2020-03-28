<?php

namespace lokothodida\Bank\Domain;

final class Money
{
    private int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    public function add(Money $money): Money
    {
        return new Money($this->amount + $money->amount);
    }

    public function subtract(Money $money): Money
    {
        return new Money($this->amount - $money->amount);
    }

    public function lessThan(Money $money): bool
    {
        return $this->amount < $money->amount;
    }
}
