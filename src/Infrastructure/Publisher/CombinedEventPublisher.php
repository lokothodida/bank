<?php

namespace lokothodida\Bank\Infrastructure\Publisher;

use lokothodida\Bank\Domain\Event;

final class CombinedEventPublisher implements EventPublisher
{
    /**
     * @var EventPublisher[]
     */
    private array $publishers;

    public function __construct(EventPublisher...$publishers)
    {
        $this->publishers = $publishers;
    }

    public function publish(string $accountId, string $customerId, Event $event): void
    {
        foreach ($this->publishers as $publisher) {
            $publisher->publish($accountId, $customerId, $event);
        }
    }
}
