<?php

namespace lokothodida\Bank\Clocks;

use lokothodida\Bank\Clock;
use DateTimeImmutable as TimeStamp;

final class AlwaysSameTimeClock implements Clock
{
    private $time;

    public function __construct(TimeStamp $time)
    {
        $this->time = $time;
    }

    public function now(): TimeStamp
    {
        return $this->time;
    }
}
