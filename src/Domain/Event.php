<?php

namespace lokothodida\Bank\Domain;

use DateTimeInterface;

abstract class Event
{
    abstract public function occurredAt(): DateTimeInterface;
}
