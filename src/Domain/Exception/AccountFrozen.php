<?php

namespace lokothodida\Bank\Domain\Exception;

use DomainException;
use Throwable;

final class AccountFrozen extends DomainException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Account frozen', 0, $previous);
    }
}
