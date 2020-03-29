<?php

namespace lokothodida\Bank\Http;

use lokothodida\Bank\OpenAccount;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PostAccounts
{
    private OpenAccount $openAccount;

    public function __construct(OpenAccount $openAccount)
    {
        $this->openAccount = $openAccount;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $customerId = $request->getHeader('X-Customer-ID')[0];
        $accountId = ($this->openAccount)($customerId);
        $response->getBody()->write((string) json_encode([
            'account_id' => $accountId,
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
