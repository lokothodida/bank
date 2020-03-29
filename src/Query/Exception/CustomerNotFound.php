<?php

namespace lokothodida\Bank\Query\Exception;

use Throwable;

final class CustomerNotFound extends \Exception
{
    public function __construct(string $customerId, Throwable $previous = null)
    {
        parent::__construct(sprintf('Customer with ID %s not found', $customerId), 0, $previous);
    }
}
