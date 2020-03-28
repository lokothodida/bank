<?php

namespace lokothodida\Bank\Domain\Exception;

use Throwable;

final class AccountClosed extends DomainException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Account closed', 0, $previous);
    }
}
