<?php

use PHPUnit\Framework\TestCase;
use lokothodida\BankSqliteAdapter\SqliteEventSourcedVault;
use lokothodida\Bank\{ Account, AccountNumber, Version, Vault, Money };
use lokothodida\Bank\Transactions\{
    AccountOpened,
    FundsDepositedIntoAccount,
    FundsWithdrawnFromAccount,
    AccountFrozen,
    FundsTransferredBetweenAccounts
};
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use DateTimeImmutable as TimeStamp;

final class SqliteEventSourcedVaultTest extends TestCase
{
    /** @var PDO */
    private $database;

    /** @var SqliteEventSourcedVault */
    private $vault;

    public function setUp()
    {
        $this->database = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $manager = new Manager(
            new Config([
                'paths' => [
                    'migrations' => __DIR__ . '/../migrations/'
                ],
                'environments' => [
                    'test' => [
                        'adapter'    => 'sqlite',
                        'connection' => $this->database
                    ]
                ]
            ]),
            new StringInput(' '),
            new NullOutput()
        );
        $manager->migrate('test');
        $this->database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

        $this->vault = new SqliteEventSourcedVault($this->database);
    }

    public function testRecordsWhenAccountsAreOpened()
    {
        $this->vault->recordThatAccountWasOpened(new AccountOpened(
            new AccountNumber('12345678'),
            new Version(1),
            new TimeStamp('2001-05-09')
        ));

        $this->databaseStoredRecordInTransactionsTable([
            'account_number' => '12345678',
            'version' => 1,
            'type' => 'account_opened',
            'recorded_at' => (new TimeStamp('2001-05-09'))->format('c'),
            'payload' => '{}',
        ]);

        $this->assertEquals(
            new Account(new AccountNumber('12345678'), new Version(1), Money::Gbp(0), false),
            $this->vault->findAccountByAccountNumber(new AccountNumber('12345678'))
        );
    }

    public function testRecordsWhenFundsAreDepositedIntoAccounts()
    {
        $this->vault->recordThatAccountWasOpened(new AccountOpened(
            new AccountNumber('12345670'),
            new Version(1),
            new TimeStamp('2001-05-09')
        ));

        $this->vault->recordThatFundsWereDeposited(new FundsDepositedIntoAccount(
            new AccountNumber('12345670'),
            new Version(2),
            Money::Gbp(500),
            new TimeStamp('2001-08-20')
        ));

        $this->databaseStoredRecordInTransactionsTable([
            'account_number' => '12345670',
            'version' => 2,
            'type' => 'funds_deposited',
            'recorded_at' => (new TimeStamp('2001-08-20'))->format('c'),
            'payload' => '{"amount":500}',
        ]);

        $this->assertEquals(
            new Account(new AccountNumber('12345670'), new Version(2), Money::Gbp(500), false),
            $this->vault->findAccountByAccountNumber(new AccountNumber('12345670'))
        );
    }

    public function testRecordsWhenFundsAreWithdrawnFromAccounts()
    {
        $this->vault->recordThatAccountWasOpened(new AccountOpened(
            new AccountNumber('12395670'),
            new Version(1),
            new TimeStamp('2011-10-15')
        ));

        $this->vault->recordThatFundsWereDeposited(new FundsDepositedIntoAccount(
            new AccountNumber('12395670'),
            new Version(2),
            Money::Gbp(2000),
            new TimeStamp('2012-03-03')
        ));

        $this->vault->recordThatFundsWereWithdrawn(new FundsWithdrawnFromAccount(
            new AccountNumber('12395670'),
            new Version(3),
            Money::Gbp(300),
            new TimeStamp('2012-03-03')
        ));

        $this->databaseStoredRecordInTransactionsTable([
            'account_number' => '12395670',
            'version' => 3,
            'type' => 'funds_withdrawn',
            'recorded_at' => (new TimeStamp('2012-03-03'))->format('c'),
            'payload' => '{"amount":300}',
        ]);

        $this->assertEquals(
            new Account(new AccountNumber('12395670'), new Version(3), Money::Gbp(1700), false),
            $this->vault->findAccountByAccountNumber(new AccountNumber('12395670'))
        );
    }

    public function testRecordsWhenFundsAreTransferredBetweenAccounts()
    {
        $this->vault->recordThatAccountWasOpened(new AccountOpened(
            new AccountNumber('12395670'),
            new Version(1),
            new TimeStamp('2011-10-15')
        ));

        $this->vault->recordThatAccountWasOpened(new AccountOpened(
            new AccountNumber('22395670'),
            new Version(1),
            new TimeStamp('2011-10-15')
        ));

        $this->vault->recordThatFundsWereDeposited(new FundsDepositedIntoAccount(
            new AccountNumber('12395670'),
            new Version(2),
            Money::Gbp(2000),
            new TimeStamp('2012-03-03')
        ));

        $this->vault->recordThatFundsWereTransferred(new FundsTransferredBetweenAccounts(
            new AccountNumber('12395670'),
            new Version(3),
            new AccountNumber('22395670'),
            new Version(2),
            Money::Gbp(700),
            new TimeStamp('2014-07-03')
        ));

        $this->databaseStoredRecordInTransactionsTable([
            'account_number' => '12395670',
            'version' => 3,
            'type' => 'funds_transferred_out',
            'recorded_at' => (new TimeStamp('2014-07-03'))->format('c'),
            'payload' => '{"to":"22395670","amount":700}',
        ]);

        $this->databaseStoredRecordInTransactionsTable([
            'account_number' => '22395670',
            'version' => 2,
            'type' => 'funds_transferred_in',
            'recorded_at' => (new TimeStamp('2014-07-03'))->format('c'),
            'payload' => '{"from":"12395670","amount":700}',
        ]);

        $this->assertEquals(
            new Account(new AccountNumber('12395670'), new Version(3), Money::Gbp(1300), false),
            $this->vault->findAccountByAccountNumber(new AccountNumber('12395670'))
        );

        $this->assertEquals(
            new Account(new AccountNumber('22395670'), new Version(2), Money::Gbp(700), false),
            $this->vault->findAccountByAccountNumber(new AccountNumber('22395670'))
        );
    }

    public function testRecordsWhenAccountsAreFrozen()
    {
        $this->vault->recordThatAccountWasOpened(new AccountOpened(
            new AccountNumber('12395670'),
            new Version(1),
            new TimeStamp('2011-10-15')
        ));

        $this->vault->recordThatAccountWasFrozen(new AccountFrozen(
            new AccountNumber('12395670'),
            new Version(2),
            new TimeStamp('2012-03-03')
        ));

        $this->databaseStoredRecordInTransactionsTable([
            'account_number' => '12395670',
            'version' => 2,
            'type' => 'account_frozen',
            'recorded_at' => (new TimeStamp('2012-03-03'))->format('c'),
            'payload' => '{}',
        ]);

        $this->assertEquals(
            new Account(new AccountNumber('12395670'), new Version(2), Money::Gbp(0), true),
            $this->vault->findAccountByAccountNumber(new AccountNumber('12395670'))
        );
    }

    /**
     * @expectedException Exception
     */
    public function testItDoesNotRecordTheTransactionWhenItHasAnOutOfDateVersion()
    {
        $this->vault->recordThatAccountWasOpened(new AccountOpened(
            new AccountNumber('12395670'),
            new Version(1),
            new TimeStamp('2011-10-15')
        ));

        $this->vault->recordThatAccountWasFrozen(new AccountFrozen(
            new AccountNumber('12395670'),
            new Version(1),
            new TimeStamp('2012-03-03')
        ));
    }

    private function databaseStoredRecordInTransactionsTable(array $row)
    {
        $statement = $this->database->prepare("
            SELECT COUNT(*)
            FROM bank_transactions
            WHERE
                account_number = :account_number
                AND version = :version
                AND type = :type
                AND recorded_at = :recorded_at
                AND payload = :payload
        ");

        $statement->execute([
            ':account_number' => $row['account_number'],
            ':version' => $row['version'],
            ':type' => $row['type'],
            ':recorded_at' => $row['recorded_at'],
            ':payload' => $row['payload']
        ]);

        $this->assertEquals(1, $statement->fetchColumn());
    }
}
