<?php

namespace lokothodida\Bank\Infrastructure\Storage;

use lokothodida\Bank\Domain\Event;
use lokothodida\Bank\Infrastructure\Publisher\EventPublisher;
use lokothodida\Bank\Query\Exception\AccountNotFound;
use lokothodida\Bank\Query\GetTransactions;
use lokothodida\Bank\Domain\Event\AccountOpened;
use lokothodida\Bank\Domain\Event\FundsDeposited;
use lokothodida\Bank\Domain\Event\FundsWithdrawn;
use lokothodida\Bank\Query\Transaction;

final class InMemoryGetTransactions implements GetTransactions, EventPublisher
{
    /**
     * @var Transaction[][]
     */
    private array $accounts = [];

    public function __invoke(string $accountId): array
    {
        if (!isset($this->accounts[$accountId])) {
            throw new AccountNotFound($accountId);
        }

        return $this->accounts[$accountId];
    }

    public function publish(string $accountId, string $customerId, Event $event): void
    {
        switch (get_class($event)) {
            case AccountOpened::class:
                $this->accounts[$accountId] = [];
                break;
            case FundsDeposited::class:
                $transaction = new Transaction();
                $transaction->amount = $event->funds()->amount();
                $transaction->date = $event->occurredAt()->format('c');

                array_unshift($this->accounts[$accountId], $transaction);
                break;
            case FundsWithdrawn::class:
                $transaction = new Transaction();
                $transaction->amount = -$event->funds()->amount();
                $transaction->date = $event->occurredAt()->format('c');

                array_unshift($this->accounts[$accountId], $transaction);
                break;
        }
    }
}
