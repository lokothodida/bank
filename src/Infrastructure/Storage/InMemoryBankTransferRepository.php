<?php

namespace lokothodida\Bank\Infrastructure\Storage;

use lokothodida\Bank\Domain\BankTransfer;
use lokothodida\Bank\Domain\BankTransferRepository;

final class InMemoryBankTransferRepository implements BankTransferRepository
{
    /**
     * @var BankTransfer[]
     */
    private array $transfers = [];

    public function newTransferId(): string
    {
        return (string) count($this->transfers);
    }

    public function get(string $transferId): BankTransfer
    {
        if (!isset($this->transfers[$transferId])) {
            throw new \DomainException('Transfer not found');
        }

        return $this->transfers[$transferId];
    }

    public function set(string $transferId, BankTransfer $transfer): void
    {
        $this->transfers[$transferId] = $transfer;
    }
}
