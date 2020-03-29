<?php

namespace lokothodida\Bank\Domain\Exception;

use Throwable;

final class AccountNotFound extends DomainException
{
    private string $accountId;

    public function __construct(string $accountId, Throwable $previous = null)
    {
        parent::__construct(sprintf('Account with ID %s not found', $accountId), 0, $previous);
        $this->accountId = $accountId;
    }

    public function accountId(): string
    {
        return $this->accountId;
    }
}
