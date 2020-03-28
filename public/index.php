<?php

use lokothodida\Bank\Command\DepositIntoAccount;
use lokothodida\Bank\Command\OpenAccount;
use lokothodida\Bank\Command\WithdrawFromAccount;
use lokothodida\Bank\Http\GetAccountBalance;
use lokothodida\Bank\Http\GetAccounts;
use lokothodida\Bank\Http\GetAccountTransactions;
use lokothodida\Bank\Http\PostAccountDeposit;
use lokothodida\Bank\Http\PostAccounts;
use lokothodida\Bank\Http\PostAccountWithdrawal;
use lokothodida\Bank\Infrastructure\Clock\LocalClock;
use lokothodida\Bank\Infrastructure\Publisher\CombinedEventPublisher;
use lokothodida\Bank\Infrastructure\Storage\EventPublishingAccountRepository;
use lokothodida\Bank\Infrastructure\Storage\InMemoryAccountRepository;
use lokothodida\Bank\Infrastructure\Storage\InMemoryGetAccountBalance;
use lokothodida\Bank\Infrastructure\Storage\InMemoryGetAccounts;
use lokothodida\Bank\Infrastructure\Storage\InMemoryGetTransactions;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$accounts = new EventPublishingAccountRepository(
    new CombinedEventPublisher(
        $getAccounts = new InMemoryGetAccounts(),
        $getBalance = new InMemoryGetAccountBalance(),
        $getTransactions = new InMemoryGetTransactions(),
    ),
    new InMemoryAccountRepository()
);
$clock = new LocalClock();

$openAccount = new OpenAccount($accounts, $clock);
$depositIntoAccount = new DepositIntoAccount($accounts, $clock);
$withdrawFromAccount = new WithdrawFromAccount($accounts, $clock);

$app = AppFactory::create();

// open account
$app->post('/accounts', new PostAccounts($openAccount));

// list all accounts
$app->get('/accounts', new GetAccounts($getAccounts));

// deposit
$app->post('/accounts/{accountId}/deposit', new PostAccountDeposit($depositIntoAccount));

// withdrawal
$app->post('/accounts/{accountId}/withdrawal', new PostAccountWithdrawal($withdrawFromAccount));

// balance
$app->get('/accounts/{accountId}/balance', new GetAccountBalance($getBalance));

// transactions
$app->get('/accounts/{accountId}/transactions', new GetAccountTransactions($getTransactions));

$loop = React\EventLoop\Factory::create();

$server = new React\Http\Server(function (Psr\Http\Message\ServerRequestInterface $request) use ($app) {
    try {
        return $app->handle($request);
    } catch (\Exception $e) {
        return new React\Http\Response(
        500,
            array('Content-Type' => 'text/plain'),
            $e->getMessage() . ": " . $e->getTraceAsString()
        );
    }
});

$port = getenv('HTTP_PORT');
$socket = new React\Socket\Server(sprintf('0.0.0.0:%s', $port), $loop);
$server->listen($socket);

echo "Server running on port $port\n";

$loop->run();
