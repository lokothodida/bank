<?php

namespace lokothodida\Bank;

use DomainException;

final class Version
{
    private $number;

    public function __construct(int $number)
    {
        if ($number < 1) {
            throw new DomainException('Versions start at 1');
        }

        $this->number = $number;
    }

    public function number(): int
    {
        return $this->number;
    }

    public function next(): Version
    {
        return new Version($this->number + 1);
    }
}
