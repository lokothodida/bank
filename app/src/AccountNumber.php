<?php

namespace lokothodida\Bank;

use DomainException;

final class AccountNumber
{
    private $accountNumber;

    public function __construct(string $accountNumber)
    {
        if (!is_numeric($accountNumber) || strlen($accountNumber) !== 8) {
            throw new DomainException('Invalid account number');
        }

        $this->accountNumber = $accountNumber;
    }

    public function __toString(): string
    {
        return $this->accountNumber;
    }
}
