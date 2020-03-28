<?php

namespace lokothodida\Bank\Command;

use lokothodida\Bank\Domain\AccountRepository;

final class OpenAccount
{
    private AccountRepository $accounts;

    public function __construct(AccountRepository $accounts)
    {
        $this->accounts = $accounts;
    }

    public function __invoke(): string
    {
        return "not implemented";
    }
}
