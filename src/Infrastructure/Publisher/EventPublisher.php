<?php

namespace lokothodida\Bank\Infrastructure\Publisher;

use lokothodida\Bank\Domain\Event;

interface EventPublisher
{
    public function publish(string $accountId, string $customerId, Event $event): void;
}
