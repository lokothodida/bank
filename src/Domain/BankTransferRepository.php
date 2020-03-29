<?php

namespace lokothodida\Bank\Domain;

interface BankTransferRepository
{
    public function newTransferId(): string;
    public function get(string $transferId): BankTransfer;
    public function set(string $transferId, BankTransfer $transfer): void;
}
