<?php

namespace lokothodida\Bank\Infrastructure\Publisher;

use lokothodida\Bank\Domain\Event;
use lokothodida\Bank\Domain\EventPublisher;

final class InMemoryDomainEventPublisher implements EventPublisher
{
    /**
     * @var callable[][]
     */
    private array $callbacks;

    public function on(string $eventName, callable $callback): void
    {
        if (!isset($this->callbacks[$eventName])) {
            $this->callbacks[$eventName] = [];
        }

        $this->callbacks[$eventName][] = $callback;
    }

    public function publish(Event $event): void
    {
        $name = get_class($event);

        if (!isset($this->callbacks[$name])) {
            return;
        }

        foreach ($this->callbacks[$name] as $callback) {
            $callback($event);
        }
    }
}
