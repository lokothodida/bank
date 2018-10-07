<?php

namespace lokothodida\Bank;

final class Currency
{
    private $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }
}
