<?php

namespace lokothodida\Bank\Domain\Exception;

use DomainException;
use Throwable;

final class InsufficientFunds extends DomainException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Insufficient funds', 0, $previous);
    }
}
