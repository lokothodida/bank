<?php

namespace lokothodida\Bank;

use DomainException;

final class Money
{
    private $amount;
    private $currency;

    private function __construct(int $amount, Currency $currency)
    {
        if ($amount < 0) {
            throw new DomainException('Amount cannot be negative');
        }

        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function Gbp(int $amount): Money
    {
        return new Money($amount, new Currency('GBP'));
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function add(Money $money): Money
    {
        $this->mustHaveTheSameCurrency($money->currency);

        return new Money($this->amount + $money->amount, $this->currency);
    }

    public function subtract(Money $money): Money
    {
        $this->mustHaveTheSameCurrency($money->currency);

        return new Money($this->amount - $money->amount, $this->currency);
    }

    public function lessThan(Money $money): bool
    {
        $this->mustHaveTheSameCurrency($money->currency);

        return $this->amount < $money->amount;
    }

    private function mustHaveTheSameCurrency(Currency $currency): void
    {
        if ($this->currency != $currency) {
            throw new DomainException('Mismatching currencies');
        }
    }
}
