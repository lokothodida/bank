<?php

namespace lokothodida\Bank\Http;

use lokothodida\Bank\Query\GetAccountBalance as GetAccountBalanceQuery;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GetAccountBalance
{
    private GetAccountBalanceQuery $query;

    public function __construct(GetAccountBalanceQuery $query)
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
        $balance = ($this->query)($args['accountId']);

        $response->getBody()->write((string) json_encode($balance));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
