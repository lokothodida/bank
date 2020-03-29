<?php

namespace lokothodida\Bank\Infrastructure\Storage;

use lokothodida\Bank\Domain\BankTransfer;
use lokothodida\Bank\Domain\BankTransferRepository;
use lokothodida\Bank\Domain\Event;
use lokothodida\Bank\Domain\EventPublisher;

final class EventPublishingBankTransferRepository implements BankTransferRepository
{
    private BankTransferRepository $repo;
    private InMemoryBankTransferRepository $cache;
    private EventPublisher $publisher;

    public function __construct(BankTransferRepository $repo, EventPublisher $publisher)
    {
        $this->repo = $repo;
        $this->cache = new InMemoryBankTransferRepository();
        $this->publisher = $publisher;
    }

    public function newTransferId(): string
    {
        return $this->repo->newTransferId();
    }

    public function get(string $transferId): BankTransfer
    {
        $transfer = $this->repo->get($transferId);
        $this->cache->set($transferId, $transfer);

        return $transfer;
    }

    public function set(string $transferId, BankTransfer $transfer): void
    {
        try {
            $previous = $this->cache->get($transferId)->events();
        } catch (\Exception $e) {
            $previous = [];
        }

        $this->repo->set($transferId, $transfer);
        $this->cache->set($transferId, $transfer);

        $current = $transfer->events();
        $newEvents = $this->diff($previous, $current);

        foreach ($newEvents as $event) {
            $this->publisher->publish($event);
        }
    }

    /**
     * @param Event[] $previous
     * @param Event[] $current
     * @return Event[]
     */
    private function diff(array $previous, array $current): array
    {
        return array_slice($current, count($previous));
    }
}
