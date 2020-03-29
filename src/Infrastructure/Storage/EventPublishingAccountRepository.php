<?php

namespace lokothodida\Bank\Infrastructure\Storage;

use lokothodida\Bank\Domain\Account;
use lokothodida\Bank\Domain\AccountRepository;
use lokothodida\Bank\Infrastructure\Publisher\EventPublisher;
use lokothodida\Bank\Domain\Event\AccountOpened;

final class EventPublishingAccountRepository implements AccountRepository
{
    private EventPublisher $publisher;
    private AccountRepository $accounts;

    public function __construct(EventPublisher $publisher, AccountRepository $accounts)
    {
        $this->publisher = $publisher;
        $this->accounts = $accounts;
    }

    public function newAccountId(): string
    {
        return $this->accounts->newAccountId();
    }

    public function get(string $accountId): Account
    {
        return $this->accounts->get($accountId);
    }

    public function set(string $accountId, Account $account): void
    {
        $this->accounts->set($accountId, $account);

        $history = $account->history();

        /** @var AccountOpened $opened */
        $opened = $history[0];

        foreach ($account->history() as $event) {
            $this->publisher->publish($accountId, $opened->customerId(), $event);
        }
    }
}
