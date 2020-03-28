<?php

namespace lokothodida\Bank\Query\Exception;

use Throwable;

final class AccountNotFound extends \Exception
{
    public function __construct(string $accountId, Throwable $previous = null)
    {
        parent::__construct(sprintf('Account with ID %s not found', $accountId), 0, $previous);
    }
}
