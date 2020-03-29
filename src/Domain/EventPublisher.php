<?php

namespace lokothodida\Bank\Domain;

interface EventPublisher
{
    public function on(string $eventName, callable $callback): void;
    public function publish(Event $event): void;
}
