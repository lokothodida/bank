<?php

namespace lokothodida\BankTest;

use DateTimeImmutable;
use lokothodida\Bank\DepositIntoAccount;
use lokothodida\Bank\Domain\Account;
use lokothodida\Bank\Domain\Money;
use lokothodida\Bank\Infrastructure\Clock\StaticClock;
use lokothodida\Bank\Infrastructure\Publisher\InMemoryDomainEventPublisher;
use lokothodida\Bank\Infrastructure\Storage\EventPublishingBankTransferRepository;
use lokothodida\Bank\Infrastructure\Storage\InMemoryAccountRepository;
use lokothodida\Bank\Infrastructure\Storage\InMemoryBankTransferRepository;
use lokothodida\Bank\TransferFundsBetweenAccounts;
use lokothodida\Bank\WithdrawFromAccount;
use PHPUnit\Framework\TestCase;

final class TransferringFundsTest extends TestCase
{
    public function testFundsCanBeTransferredBetweenTwoOpenAccountsWhenSenderHasSufficientFunds(): void
    {
        $now = new DateTimeImmutable();
        $accounts = new InMemoryAccountRepository();
        $accounts->set(
            'sender-account-id',
            Account::open('sender-account-id', 'c1', $now)
                ->deposit(new Money(50), $now),
        );
        $accounts->set('recipient-account-id', Account::open('recipient-account-id', 'c2', $now));

        $clock = new StaticClock($now);
        $publisher = new InMemoryDomainEventPublisher();
        $command = new TransferFundsBetweenAccounts(
            new WithdrawFromAccount($accounts, $clock),
            new DepositIntoAccount($accounts, $clock),
            $clock,
            $publisher,
            new EventPublishingBankTransferRepository(new InMemoryBankTransferRepository(), $publisher),
        );
        $command('sender-account-id', 'recipient-account-id', 25);

        $this->assertEquals(
            Account::open('sender-account-id', 'c1', $now)
                ->deposit(new Money(50), $now)
                ->withdraw(new Money(25), $now),
            $accounts->get('sender-account-id'),
            'Sender account does not have correct funds'
        );

        $this->assertEquals(
            Account::open('recipient-account-id', 'c2', $now)
                ->deposit(new Money(25), $now),
            $accounts->get('recipient-account-id'),
            'Recipient account does not have correct funds'
        );
    }

    public function testWhenTheTransferInFailsTheSenderIsRefunded(): void
    {
        $now = new DateTimeImmutable();
        $accounts = new InMemoryAccountRepository();
        $accounts->set(
            'sender-account-id',
            Account::open('sender-account-id', 'c1', $now)
                ->deposit(new Money(50), $now),
            );
        $accounts->set(
            'recipient-account-id',
            Account::open('recipient-account-id', 'c2', $now)
                ->close($now)
        );

        $clock = new StaticClock($now);
        $publisher = new InMemoryDomainEventPublisher();
        $command = new TransferFundsBetweenAccounts(
            new WithdrawFromAccount($accounts, $clock),
            new DepositIntoAccount($accounts, $clock),
            $clock,
            $publisher,
            new EventPublishingBankTransferRepository(new InMemoryBankTransferRepository(), $publisher),
        );
        $command('sender-account-id', 'recipient-account-id', 25);

        $this->assertEquals(
            Account::open('sender-account-id', 'c1', $now)
                ->deposit(new Money(50), $now)
                ->withdraw(new Money(25), $now)
                ->deposit(new Money(25), $now),
            $accounts->get('sender-account-id'),
            'Sender account does not have correct funds'
        );

        $this->assertEquals(
            Account::open('recipient-account-id', 'c2', $now)
                ->close($now),
            $accounts->get('recipient-account-id'),
            'Recipient account does not have correct funds'
        );
    }
}
