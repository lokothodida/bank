<?php


namespace lokothodida\Bank\Http;

use lokothodida\Bank\Query\GetTransactions;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetAccountTransactions
{
    private GetTransactions $query;

    public function __construct(GetTransactions $query)
    {
        $this->query = $query;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $transactions = ($this->query)($args['accountId']);

        $response->getBody()->write((string) json_encode($transactions));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
