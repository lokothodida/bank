<?php

namespace lokothodida\Bank;

use DateTimeImmutable as TimeStamp;

interface Clock
{
    public function now(): TimeStamp;
}
