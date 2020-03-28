<?php

namespace lokothodida\Bank\Domain;

use DateTimeInterface;

interface Clock
{
    public function now(): DateTimeInterface;
}
