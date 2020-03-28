<?php

namespace lokothodida\Bank\Infrastructure\Storage;

use lokothodida\Bank\Domain\Account;
use lokothodida\Bank\Domain\AccountRepository;
use lokothodida\Bank\Domain\Event;
use lokothodida\Bank\Domain\Event\AccountOpened;
use lokothodida\Bank\Infrastructure\Publisher\EventPublisher;
use lokothodida\Bank\Query\Account as AccountView;
use lokothodida\Bank\Query\GetAccounts;

final class InMemoryGetAccounts implements GetAccounts, EventPublisher
{
    /**
     * @var AccountView[][]
     */
    private array $customers = [];

    public function __invoke(string $customerId): array
    {
        if (!isset($this->customers[$customerId])) {
            return [];
        }

        return $this->customers[$customerId];
    }

    public function publish(string $accountId, string $customerId, Event $event): void
    {
        switch (get_class($event)) {
            case AccountOpened::class:
                if (!isset($this->customers[$customerId])) {
                    $this->customers[$customerId] = [];
                }

                $view = new AccountView();
                $view->account_id = $accountId;
                $view->customer_id = $customerId;
                $this->customers[$customerId][$accountId] = $view;
                break;
            default:
        }
    }
}
