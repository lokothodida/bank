<?php

namespace lokothodida\BankSqliteAdapter;

use lokothodida\Bank\{ Account, AccountNumber, Version, Money, Vault };
use lokothodida\Bank\Transactions\{
    AccountOpened,
    FundsDepositedIntoAccount,
    FundsWithdrawnFromAccount,
    AccountFrozen,
    FundsTransferredBetweenAccounts
};
use PDO;
use DomainException;
use Exception;

final class SqliteEventSourcedVault implements Vault
{
    private $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    public function generateAccountNumber(): AccountNumber
    {

    }

    public function findAccountByAccountNumber(AccountNumber $accountNumber): Account
    {
        $transactions = $this->findAllTransactionsForAccount((string)$accountNumber);

        $state = [ 'version' => 1, 'is_frozen' => false, 'balance' => 0 ];

        foreach ($transactions as $transaction) {
            $payload = json_decode($transaction['payload'], true);

            switch ($transaction['type']) {
                case 'account_opened':
                    $state['balance'] = 0;
                    break;
                case 'funds_deposited':
                case 'funds_transferred_in':
                    $state['balance'] += (int)$payload['amount'];
                    break;
                case 'funds_withdrawn':
                case 'funds_transferred_out':
                    $state['balance'] -= (int)$payload['amount'];
                    break;
                case 'account_frozen':
                    $state['is_frozen'] = true;
                    break;
                default:
                    throw new DomainException(
                        sprintf('Transaction not recognized: "%s"', $transaction['type'])
                    );
            }

            $state['version'] = $transaction['version'];
        }

        return new Account(
            new AccountNumber($accountNumber),
            new Version($state['version']),
            Money::Gbp($state['balance']),
            $state['is_frozen']
        );
    }

    public function recordThatAccountWasOpened(AccountOpened $occurred): void
    {
        $this->insertRow([
            'account_number' => (string)$occurred->accountNumber(),
            'version' => $occurred->version()->number(),
            'type' => 'account_opened',
            'recorded_at' => $occurred->at()->format('c'),
            'payload' => json_encode((object)[]),
        ]);
    }

    public function recordThatFundsWereDeposited(FundsDepositedIntoAccount $occurred): void
    {
        $this->insertRow([
            'account_number' => (string)$occurred->accountNumber(),
            'version' => $occurred->version()->number(),
            'type' => 'funds_deposited',
            'recorded_at' => $occurred->at()->format('c'),
            'payload' => json_encode(['amount' => $occurred->funds()->amount()]),
        ]);
    }

    public function recordThatFundsWereWithdrawn(FundsWithdrawnFromAccount $occurred): void
    {
        $this->insertRow([
            'account_number' => (string)$occurred->accountNumber(),
            'version' => $occurred->version()->number(),
            'type' => 'funds_withdrawn',
            'recorded_at' => $occurred->at()->format('c'),
            'payload' => json_encode(['amount' => $occurred->funds()->amount()]),
        ]);
    }

    public function recordThatFundsWereTransferred(FundsTransferredBetweenAccounts $occurred): void
    {
        $this->insertRows(
            [
                'account_number' => (string)$occurred->fromAccountNumber(),
                'version' => $occurred->fromVersion()->number(),
                'type' => 'funds_transferred_out',
                'recorded_at' => $occurred->at()->format('c'),
                'payload' => json_encode([
                    'to' => (string)$occurred->toAccountNumber(),
                    'amount' => $occurred->funds()->amount()
                ]),
            ], [
                'account_number' => (string)$occurred->toAccountNumber(),
                'version' => $occurred->toVersion()->number(),
                'type' => 'funds_transferred_in',
                'recorded_at' => $occurred->at()->format('c'),
                'payload' => json_encode([
                    'from' => (string)$occurred->fromAccountNumber(),
                    'amount' => $occurred->funds()->amount()
                ]),
            ]
        );
    }

    public function recordThatAccountWasFrozen(AccountFrozen $occurred): void
    {
        $this->insertRow([
            'account_number' => (string)$occurred->accountNumber(),
            'version' => $occurred->version()->number(),
            'type' => 'account_frozen',
            'recorded_at' => $occurred->at()->format('c'),
            'payload' => json_encode((object)[]),
        ]);
    }

    private function findAllTransactionsForAccount(string $accountNumber): iterable
    {
        $statement = $this->database->prepare("
            SELECT *
            FROM bank_transactions
            WHERE account_number = :account_number
            ORDER BY recorded_at ASC
        ");
        $statement->execute([ ':account_number' => $accountNumber ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function insertRow(array $row)
    {
        $this->versionMustBeUpToDate($row['account_number'], $row['version']);

        $this->database->prepare("
                INSERT INTO bank_transactions(account_number, version, type, recorded_at, payload)
                VALUES (:account_number, :version, :type, :recorded_at, :payload)
            ")->execute([
                ':account_number' => $row['account_number'],
                ':version' => $row['version'],
                ':type' => $row['type'],
                ':recorded_at' => $row['recorded_at'],
                ':payload' => $row['payload'],
            ]);
    }

    private function insertRows(array $from, array $to)
    {
        try {
            $this->database->beginTransaction();

            $this->versionMustBeUpToDate($from['account_number'], $from['version']);
            $this->versionMustBeUpToDate($to['account_number'], $to['version']);

            $this->database->prepare("
                    INSERT INTO bank_transactions(account_number, version, type, recorded_at, payload)
                    VALUES
                        (:from_account_number, :from_version, :from_type, :from_recorded_at, :from_payload),
                        (:to_account_number, :to_version, :to_type, :to_recorded_at, :to_payload)
                ")->execute([
                    ':from_account_number' => $from['account_number'],
                    ':from_version' => $from['version'],
                    ':from_type' => $from['type'],
                    ':from_recorded_at' => $from['recorded_at'],
                    ':from_payload' => $from['payload'],
                    ':to_account_number' => $to['account_number'],
                    ':to_version' => $to['version'],
                    ':to_type' => $to['type'],
                    ':to_recorded_at' => $to['recorded_at'],
                    ':to_payload' => $to['payload'],
                ]);
            $this->database->commit();
        } catch (Exception $exception) {
            $this->database->rollback();
            throw $exception;
        }
    }

    private function versionMustBeUpToDate(string $accountNumber, int $version): void
    {
        $statement = $this->database->prepare("
            SELECT COUNT(*)
            FROM bank_transactions
            WHERE
                account_number = :account_number
                AND version >= :version
        ");

        $statement->execute([ $accountNumber, $version ]);

        if ($statement->fetchColumn() > 0) {
            throw new Exception('Account version out of date');
        }
    }
}
