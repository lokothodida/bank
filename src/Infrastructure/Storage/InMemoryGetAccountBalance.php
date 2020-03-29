<?php


namespace lokothodida\Bank\Infrastructure\Storage;

use lokothodida\Bank\Domain\Event;
use lokothodida\Bank\Infrastructure\Publisher\EventPublisher;
use lokothodida\Bank\Query\Model\AccountBalance;
use lokothodida\Bank\Query\Exception\AccountNotFound;
use lokothodida\Bank\Query\GetAccountBalance;
use lokothodida\Bank\Domain\Event\AccountOpened;
use lokothodida\Bank\Domain\Event\FundsDeposited;

class InMemoryGetAccountBalance implements GetAccountBalance, EventPublisher
{
    /** @var AccountBalance[] */
    private array $balances;

    public function __invoke(string $accountId): AccountBalance
    {
        if (!isset($this->balances[$accountId])) {
            throw new AccountNotFound($accountId);
        }

        return $this->balances[$accountId];
    }

    public function publish(string $accountId, string $customerId, Event $event): void
    {
        switch (get_class($event)) {
            case AccountOpened::class:
                $this->balances[$accountId] = new AccountBalance();
                $this->balances[$accountId]->balance = 0;
                break;
            case FundsDeposited::class:
                $this->balances[$accountId]->balance += $event->funds()->amount();
                break;
            case Event\FundsWithdrawn::class:
                $this->balances[$accountId]->balance -= $event->funds()->amount();
                break;
        }
    }
}
